<?php
require_once '../core/BaseController.php';
require_once '../core/Sesion.php';

class CajeroController extends BaseController {
    public function __construct() {
        Sesion::verificarRol('cajero');
    }
    
    public function dashboard() {
        $pedidoModel = $this->model('PedidoModel');
        $pagoModel = $this->model('PagoModel');
        
        $data = [
            'titulo' => 'Dashboard - Cajero',
            'pedidos_listos' => $pedidoModel->obtenerPedidosPorEstado('listo'),
            'pedidos_entregados' => $pedidoModel->obtenerPedidosPorEstado('entregado'),
            'total_dia' => $pagoModel->obtenerTotalHoy()
        ];
        
        $this->view('cajero/dashboard', $data);
    }
    
    public function pagos() {
        $pedidoModel = $this->model('PedidoModel');
        
        $data = [
            'titulo' => 'Gestión de Pagos - Cajero',
            'pedidos_pendientes' => $pedidoModel->obtenerPedidosParaPago()
        ];
        
        $this->view('cajero/pagos', $data);
    }
    
    public function procesarPago($pedido_id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pagoModel = $this->model('PagoModel');
            $metodo = $_POST['metodo'];
            $monto = $_POST['monto'];
            
            if ($pagoModel->crearPago($pedido_id, $monto, $metodo)) {
                $_SESSION['success'] = 'Pago procesado correctamente';
            } else {
                $_SESSION['error'] = 'Error al procesar pago';
            }
        }
        
        $this->redirect('cajero/pagos');
    }
    
    public function cierreCaja() {
        $pagoModel = $this->model('PagoModel');
        
        $data = [
            'titulo' => 'Cierre de Caja - Cajero',
            'ventas_dia' => $pagoModel->obtenerVentasDelDia(),
            'metodos_pago' => $pagoModel->obtenerTotalesPorMetodo()
        ];
        
        $this->view('cajero/cierre_caja', $data);
    }
}
?>