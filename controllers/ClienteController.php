<?php
require_once '../core/BaseController.php';

class ClienteController extends BaseController {
    public function __construct() {
                if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // No requiere autenticación para clientes
    }
    
    public function index() {
        $producto = $this->model('ProductoModel');
        $productos = $producto->obtenerProductos();
        
        $data = [
            'titulo' => 'Menú Principal - Cafetería Nova',
            'productos' => $productos
        ];
        
        $this->view('cliente/menu', $data);
    }
    
public function pedido() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $productos = isset($_POST['productos']) ? json_decode($_POST['productos'], true) : [];
        $tipo = $_POST['tipo'] ?? '';
        $nombreCliente = $_POST['nombre_cliente'] ?? null;

        $total = 0;
        if (is_array($productos)) {
            foreach ($productos as $producto) {
                $total += $producto['precio'] * $producto['cantidad'];
            }
        }

        // Generar código único para el pedido
        $codigo = 'PED' . date('YmdHis') . rand(100,999);
        $datosPedido = [
            'codigo' => $codigo,
            'cliente_id' => null,
            'nombre_cliente' => $nombreCliente,
            'tipo' => $tipo,
            'total' => $total,
            'productos' => is_array($productos) ? $productos : []
        ];

        $pedidoModel = $this->model('PedidoModel');
        $pedido_id = $pedidoModel->crearPedido($datosPedido);

        if ($pedido_id) {
            // Cambiar el estado del pedido a 'listo' para que el cajero lo vea
            $pedidoModel->actualizarEstado($pedido_id, 'listo');

            $notificacionModel = $this->model('NotificacionModel');
            $mensaje = "Nuevo pedido #{$pedido_id} - {$tipo} - Total: $" . number_format($total, 2);
            if ($nombreCliente) {
                $mensaje .= " - Cliente: {$nombreCliente}";
            }
            $notificacionModel->crear('nuevo_pedido', 'mesero', $mensaje, $pedido_id);

            $_SESSION['pedido_id'] = $pedido_id;
            $this->redirect('cliente/comprobante/' . $pedido_id);
        } else {
            $_SESSION['error'] = 'Error al crear el pedido';
            $this->redirect('cliente/pedido');
        }
    } else {
        $producto = $this->model('ProductoModel');
        $productos = $producto->obtenerProductos();

        $data = [
            'titulo' => 'Realizar Pedido - Cafetería Nova',
            'productos' => $productos
        ];

        $this->view('cliente/pedido', $data);
    }
}
    
    public function comprobante($id) {
        $pedidoModel = $this->model('PedidoModel');
        $pedido = $pedidoModel->obtenerPedidoPorId($id);
        $detalles = $pedidoModel->obtenerDetallesPedido($id);
        
        if (!$pedido) {
            $_SESSION['error'] = 'Pedido no encontrado';
            $this->redirect('cliente');
        }
        
        $data = [
            'titulo' => 'Comprobante de Pedido',
            'pedido' => $pedido,
            'detalles' => $detalles
        ];
        
        $this->view('cliente/comprobante', $data);
    }
}
?>
