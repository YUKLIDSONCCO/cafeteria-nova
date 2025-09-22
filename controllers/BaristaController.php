<?php
require_once '../core/BaseController.php';
require_once '../core/Sesion.php';

class BaristaController extends BaseController {
    public function __construct() {
        Sesion::verificarRol('barista');
    }
    
    public function ingredientesFaltantes() {
        $inventarioModel = $this->model('InventarioModel');
        $inventario = $inventarioModel->obtenerInventarioCompleto();
        $productos_bajos = $inventarioModel->obtenerProductosStockBajo();
        $total_valor_inventario = $inventarioModel->calcularValorTotalInventario();
        
        // Preparar los productos con stock bajo en el formato esperado
        $productos_bajos_formateados = [];
        foreach ($productos_bajos as $producto) {
            $productos_bajos_formateados[] = [
                'producto' => $producto['producto'],
                'cantidad_actual' => $producto['cantidad_actual'],
                'minimo' => $producto['minimo']
            ];
        }
        
        $this->view('barista/ingredientesFaltantes', [
            'titulo' => 'Gestión de Inventario - Barista',
            'productos_bajos' => $productos_bajos_formateados,
            'inventario' => $inventario,
            'total_valor_inventario' => $total_valor_inventario
        ]);
    }
    
    public function agregarProducto() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'barista/ingredientesFaltantes');
            return;
        }

        $datos = [
            'nombre' => $_POST['nombre'] ?? '',
            'tipo' => $_POST['tipo'] ?? '',
            'stock' => $_POST['stock'] ?? 0,
            'stock_minimo' => $_POST['stock_minimo'] ?? 0,
            'unidad_medida' => $_POST['unidad_medida'] ?? '',
            'precio' => $_POST['precio'] ?? null,
            'categoria' => $_POST['categoria'] ?? null,
            'descripcion' => $_POST['descripcion'] ?? null
        ];

        $inventarioModel = $this->model('InventarioModel');
        $resultado = $inventarioModel->agregarProducto($datos);

        if ($resultado) {
            $_SESSION['mensaje'] = 'Producto agregado correctamente';
            $_SESSION['mensaje_tipo'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al agregar el producto';
            $_SESSION['mensaje_tipo'] = 'danger';
        }

        header('Location: ' . BASE_URL . 'barista/ingredientesFaltantes');
    }

    public function actualizarInventario() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id']) || !isset($data['stock']) || !isset($data['stock_minimo']) || !isset($data['estado'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        $inventarioModel = $this->model('InventarioModel');
        $resultado = $inventarioModel->actualizarProducto($data);

        if ($resultado) {
            // Verificar si el stock está bajo y crear notificación si es necesario
            if ($data['stock'] <= $data['stock_minimo'] || $data['estado'] === 'bajo') {
                $producto = $inventarioModel->obtenerProductoPorId($data['id']);
                $notificacionModel = $this->model('NotificacionModel');
                $mensaje = "Stock bajo de {$producto['nombre']} - Quedan {$data['stock']} {$producto['unidad_medida']}";
                $notificacionModel->crear('stock_bajo', 'barista', $mensaje, $data['id']);
            }
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
        }
    }

    public function notificarFaltante($id) {
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID no válido']);
            return;
        }

        $inventarioModel = $this->model('InventarioModel');
        $producto = $inventarioModel->obtenerProductoPorId($id);
        
        if (!$producto) {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
            return;
        }

        $notificacionModel = $this->model('NotificacionModel');
        $mensaje = "¡ALERTA! Stock crítico de {$producto['nombre']} - Requiere atención inmediata";
        $resultado = $notificacionModel->crear('stock_critico', 'barista', $mensaje, $id);

        if ($resultado) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al enviar notificación']);
        }
    }
    
    public function dashboard() {
        $pedidoModel = $this->model('PedidoModel');
        
        // Función auxiliar para categorizar los productos
        $categorizarProductos = function($detalles) {
            $bebidas = [];
            $alimentos = [];
            
            foreach ($detalles as $detalle) {
                if (strpos(strtolower($detalle['categoria']), 'bebida') !== false) {
                    $bebidas[] = $detalle;
                } else {
                    $alimentos[] = $detalle;
                }
            }
            
            return [
                'bebidas' => $bebidas,
                'alimentos' => $alimentos
            ];
        };
        
        // Obtener y categorizar pedidos confirmados
        $pedidos_confirmados = $pedidoModel->obtenerPedidosPorEstado('confirmado');
        foreach ($pedidos_confirmados as &$pedido) {
            $detalles = $pedidoModel->obtenerDetallesPedido($pedido['id']);
            $pedido['productos_categorizados'] = $categorizarProductos($detalles);
        }
        
        // Obtener y categorizar pedidos en preparación
        $pedidos_preparacion = $pedidoModel->obtenerPedidosPorEstado('preparacion');
        foreach ($pedidos_preparacion as &$pedido) {
            $detalles = $pedidoModel->obtenerDetallesPedido($pedido['id']);
            $pedido['productos_categorizados'] = $categorizarProductos($detalles);
        }
        
        // Obtener y categorizar pedidos listos
        $pedidos_listos = $pedidoModel->obtenerPedidosPorEstado('listo');
        foreach ($pedidos_listos as &$pedido) {
            $detalles = $pedidoModel->obtenerDetallesPedido($pedido['id']);
            $pedido['productos_categorizados'] = $categorizarProductos($detalles);
        }
        
        $data = [
            'titulo' => 'Dashboard - Barista',
            'pedidos_confirmados' => $pedidos_confirmados,
            'pedidos_preparacion' => $pedidos_preparacion,
            'pedidos_listos' => $pedidos_listos
        ];
        
        $this->view('barista/dashboard', $data);
    }
    
    public function iniciarPreparacion($id) {
        $pedidoModel = $this->model('PedidoModel');
        
        if ($pedidoModel->actualizarEstado($id, 'preparacion')) {
            $_SESSION['success'] = 'Preparación iniciada';
        } else {
            $_SESSION['error'] = 'Error al iniciar preparación';
        }
        
        $this->redirect('barista/dashboard');
    }
    
public function finalizarPreparacion($id) {
    $pedidoModel = $this->model('PedidoModel');
    
    if ($pedidoModel->actualizarEstado($id, 'listo')) {
        // Notificar al cajero o mesero según el tipo de pedido
        $notificacionModel = $this->model('NotificacionModel');
        $pedido = $pedidoModel->obtenerPedidoPorId($id);
        $mensaje = "Pedido #{$pedido['codigo']} listo para entregar/pagar";
        
        if ($pedido['tipo'] === 'mesa') {
            $notificacionModel->crear('pedido_listo', 'mesero', $mensaje, $id);
        } else {
            $notificacionModel->crear('pedido_listo', 'cajero', $mensaje, $id);
        }

        $_SESSION['success'] = 'Pedido listo para entregar';
    } else {
        $_SESSION['error'] = 'Error al finalizar preparación';
    }
    
    $this->redirect('barista/dashboard');
}

public function obtenerPedidosJSON() {
    if (!Sesion::tieneRol('barista')) {
        header('HTTP/1.0 403 Forbidden');
        echo json_encode(['error' => 'Acceso denegado']);
        return;
    }
    
    $pedidoModel = $this->model('PedidoModel');
    
    // Obtener pedidos con sus detalles
    $pedidos_confirmados = $pedidoModel->obtenerPedidosPorEstado('confirmado');
    $pedidos_preparacion = $pedidoModel->obtenerPedidosPorEstado('preparacion');
    $pedidos_listos = $pedidoModel->obtenerPedidosPorEstado('listo');
    
    // Agregar detalles de productos a cada pedido
    foreach ($pedidos_confirmados as &$pedido) {
        $pedido['detalles'] = $pedidoModel->obtenerDetallesPedido($pedido['id']);
    }
    foreach ($pedidos_preparacion as &$pedido) {
        $pedido['detalles'] = $pedidoModel->obtenerDetallesPedido($pedido['id']);
    }
    foreach ($pedidos_listos as &$pedido) {
        $pedido['detalles'] = $pedidoModel->obtenerDetallesPedido($pedido['id']);
    }
    
    $data = [
        'pedidos_confirmados' => $pedidos_confirmados,
        'pedidos_preparacion' => $pedidos_preparacion,
        'pedidos_listos' => $pedidos_listos
    ];
    
    header('Content-Type: application/json');
    echo json_encode($data);
}

}
?>
