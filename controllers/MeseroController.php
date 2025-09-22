<?php 
require_once '../core/BaseController.php';
require_once '../core/Sesion.php';

class MeseroController extends BaseController {
    public function __construct() {
        Sesion::verificarRol('mesero');
    }
    
    public function dashboard() {
        $pedidoModel = $this->model('PedidoModel');
        $mesaModel = $this->model('MesaModel');
        
        $data = [
            'titulo' => 'Dashboard - Mesero',
            'pedidos_pendientes' => $pedidoModel->obtenerPedidosPorEstado('creado'),
            'pedidos_confirmados' => $pedidoModel->obtenerPedidosPorEstado('confirmado'),
            'mesas' => $mesaModel->obtenerMesas()
        ];
        
        $this->view('mesero/dashboard', $data);
    }

    public function pedidos() {
        $pedidoModel = $this->model('PedidoModel');
        
        $data = [
            'titulo' => 'Gestión de Pedidos - Mesero',
            'pedidos' => $pedidoModel->obtenerTodosPedidos()
        ];
        
        $this->view('mesero/pedidos', $data);
    }

    public function confirmarPedido($id) {
        $pedidoModel = $this->model('PedidoModel');
        
        if ($pedidoModel->actualizarEstado($id, 'confirmado')) {
            // Crear notificación
            $notificacionModel = $this->model('NotificacionModel');
            $pedido = $pedidoModel->obtenerPedidoPorId($id);

            // ⚡ Usar código corto en base al ID
            $codigoPedido = "NV-" . str_pad($pedido['id'], 4, "0", STR_PAD_LEFT);

            $mensaje = "Pedido {$codigoPedido} confirmado - Listo para preparar";
            $notificacionModel->crear('pedido_confirmado', 'barista', $mensaje, $id);

            $_SESSION['success'] = 'Pedido confirmado correctamente';
        } else {
            $_SESSION['error'] = 'Error al confirmar el pedido';
        }
        
        $this->redirect('mesero/dashboard');
    }
    public function asignarMesa() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pedido_id = $_POST['pedido_id'];
        $mesa_id   = $_POST['mesa_id'];

        $mesaModel   = $this->model('MesaModel');

        // Solo usamos MesaModel, ya actualiza mesa y pedido
        if ($mesaModel->asignarMesa($pedido_id, $mesa_id)) {
            $_SESSION['success'] = "Mesa asignada correctamente.";
        } else {
            $_SESSION['error'] = "Error al asignar mesa.";
        }

        header("Location: " . BASE_URL . "mesero/dashboard");
        exit;
    }
}

    public function liberarMesa() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mesa_id = $_POST['mesa_id'];
            $mesaModel = $this->model('MesaModel');
            
            if ($mesaModel->liberarMesa($mesa_id)) {
                $_SESSION['success'] = 'Mesa liberada correctamente';
            } else {
                $_SESSION['error'] = 'Error al liberar mesa';
            }
        }
        $this->redirect('mesero/mesas');
    }

    public function entregarPedido() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pedido_id = $_POST['pedido_id'];
            $pedidoModel = $this->model('PedidoModel');

            if ($pedidoModel->actualizarEstado($pedido_id, 'entregado')) {
                $_SESSION['success'] = 'Pedido entregado al cliente';
            } else {
                $_SESSION['error'] = 'Error al marcar pedido como entregado';
            }
        }
        $this->redirect('mesero/pedidos');
    }

    public function verPedido($id) {
        $pedidoModel = $this->model('PedidoModel');
        $pedido = $pedidoModel->obtenerPedidoPorId($id);

        if (!$pedido) {
            $_SESSION['error'] = 'Pedido no encontrado';
            $this->redirect('mesero/pedidos');
        }

        // Opcional: traer detalles de productos
        $detalles = [];
        if (method_exists($pedidoModel, 'obtenerDetallesPedido')) {
            $detalles = $pedidoModel->obtenerDetallesPedido($id);
        }

        $data = [
            'titulo' => 'Detalle Pedido',
            'pedido' => $pedido,
            'detalles' => $detalles
        ];

        $this->view('mesero/verPedido', $data);
    }
    public function index() {
    // Redirige al dashboard por defecto
    header("Location: " . BASE_URL . "mesero/dashboard");
    exit;
}
}
?>
