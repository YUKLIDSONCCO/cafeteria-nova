<?php
require_once '../core/BaseController.php';
require_once '../core/Sesion.php';

class BaristaController extends BaseController {
    public function __construct() {
        Sesion::verificarRol('barista');
    }
    
    public function ingredientesFaltantes() {
        $inventarioModel = $this->model('InventarioModel');
        $ingredientes = $inventarioModel->obtenerIngredientes();
        $categorias = $inventarioModel->obtenerCategorias();
        
        $this->view('barista/ingredientesFaltantes', [
            'ingredientes' => $ingredientes,
            'categorias' => $categorias
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

    public function marcarFaltante($id) {
        if (!$id) {
            $_SESSION['mensaje'] = 'ID de ingrediente no válido';
            $_SESSION['mensaje_tipo'] = 'danger';
            header('Location: ' . BASE_URL . 'barista/ingredientesFaltantes');
            return;
        }
        
        $inventarioModel = $this->model('InventarioModel');
        $resultado = $inventarioModel->marcarIngredienteFaltante($id);
        
        if ($resultado) {
            $_SESSION['mensaje'] = 'Ingrediente marcado como faltante correctamente';
            $_SESSION['mensaje_tipo'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al marcar el ingrediente como faltante';
            $_SESSION['mensaje_tipo'] = 'danger';
        }
        
        header('Location: ' . BASE_URL . 'barista/ingredientesFaltantes');
    }
    
    public function dashboard() {
        $pedidoModel = $this->model('PedidoModel');
        
        // Obtener pedidos con sus detalles
        $pedidos_confirmados = $pedidoModel->obtenerPedidosPorEstado('confirmado');
        foreach ($pedidos_confirmados as &$pedido) {
            $pedido['detalles'] = $pedidoModel->obtenerDetallesPedido($pedido['id']);
        }
        
        $pedidos_preparacion = $pedidoModel->obtenerPedidosPorEstado('preparacion');
        foreach ($pedidos_preparacion as &$pedido) {
            $pedido['detalles'] = $pedidoModel->obtenerDetallesPedido($pedido['id']);
        }
        
        $pedidos_listos = $pedidoModel->obtenerPedidosPorEstado('listo');
        foreach ($pedidos_listos as &$pedido) {
            $pedido['detalles'] = $pedidoModel->obtenerDetallesPedido($pedido['id']);
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
