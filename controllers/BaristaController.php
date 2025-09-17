<?php
require_once '../core/BaseController.php';
require_once '../core/Sesion.php';

class BaristaController extends BaseController {
    public function __construct() {
        Sesion::verificarRol('barista');
    }
    
    public function dashboard() {
        $pedidoModel = $this->model('PedidoModel');
        
        $data = [
            'titulo' => 'Dashboard - Barista',
            'pedidos_confirmados' => $pedidoModel->obtenerPedidosPorEstado('confirmado'),
            'pedidos_preparacion' => $pedidoModel->obtenerPedidosPorEstado('preparacion'),
            'pedidos_listos' => $pedidoModel->obtenerPedidosPorEstado('listo')
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

}
?>