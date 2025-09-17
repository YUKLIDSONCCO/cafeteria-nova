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
    }public function pedidos() {
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
        $mensaje = "Pedido #{$pedido['codigo']} confirmado - Listo para preparar";
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
            $mesa_id = $_POST['mesa_id'];
            
            $mesaModel = $this->model('MesaModel');
            if ($mesaModel->asignarMesa($pedido_id, $mesa_id)) {
                $_SESSION['success'] = 'Mesa asignada correctamente';
            } else {
                $_SESSION['error'] = 'Error al asignar mesa';
            }
        }
        
        $this->redirect('mesero/dashboard');
    }
}
?>