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
    
    public function reportes() {
        $pedidoModel = $this->model('PedidoModel');
        $pagoModel = $this->model('PagoModel');
        $fecha_inicio = $_GET['inicio'] ?? date('Y-m-01');
        $fecha_fin = $_GET['fin'] ?? date('Y-m-d');
        $reporte = $pedidoModel->obtenerReporteVentas($fecha_inicio, $fecha_fin);
        $total = $pagoModel->obtenerTotalHoy();
        $this->view('cajero/reportes', [
            'reporte' => $reporte,
            'total' => $total,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin
        ]);
    }
    
    // MÉTODO NUEVO PARA MANEJAR SOLICITUDES AJAX
public function reporteVentasAjax() {
    $pedidoModel = $this->model('PedidoModel');
    
    // Usar los mismos parámetros que en el reporte principal
    $fecha_inicio = $_GET['inicio'] ?? date('Y-m-d', strtotime('-6 days'));
    $fecha_fin = $_GET['fin'] ?? date('Y-m-d');
    
    $reporte = $pedidoModel->obtenerReporteVentas($fecha_inicio, $fecha_fin);
    
    // Asegurarse de devolver un array siempre
    if (!is_array($reporte)) {
        $reporte = [];
    }
    
    header('Content-Type: application/json');
    echo json_encode($reporte);
    exit;
}
    // Cambiar estado de pago del pedido
    public function togglePago() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pedido_id = $_POST['pedido_id'];
            $estado_actual = $_POST['estado_actual'];
            $pedidoModel = $this->model('PedidoModel');
            $nuevo_estado = ($estado_actual === 'pagado') ? 'no pagado' : 'pagado';
            if ($pedidoModel->actualizarEstado($pedido_id, $nuevo_estado)) {
                $_SESSION['success'] = 'Estado de pago actualizado.';
            } else {
                $_SESSION['error'] = 'No se pudo actualizar el estado.';
            }
        }
        $this->redirect('cajero/dashboard');
    }
// Agrega este método ANTES del método togglePago()
// ... otros métodos existentes ...

public function pedidosPendientesAjax() {
    $pedidoModel = $this->model('PedidoModel');
    $pendientes = $pedidoModel->obtenerPedidosParaPago();
    
    header('Content-Type: application/json');
    echo json_encode(is_array($pendientes) ? $pendientes : []);
    exit;
}

public function index() {
    // Redirigir al dashboard por defecto
    $this->dashboard();
}
}
?>
