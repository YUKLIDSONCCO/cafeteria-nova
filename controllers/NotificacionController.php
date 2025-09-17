<?php
require_once '../core/BaseController.php';
require_once '../core/Sesion.php';

class NotificacionController extends BaseController {
    public function __construct() {
        Sesion::verificarAutenticacion();
    }
    
    public function obtener() {
        header('Content-Type: application/json');
        
        $rol = $_SESSION['usuario_rol'];
        $ultima_id = $_GET['ultima_id'] ?? 0;
        
        $notificacionModel = $this->model('NotificacionModel');
        $notificaciones = $notificacionModel->obtenerPendientes($rol, $ultima_id);
        
        echo json_encode([
            'success' => true,
            'notificaciones' => $notificaciones,
            'total' => count($notificaciones)
        ]);
    }
    
    public function marcarLeida($id) {
        header('Content-Type: application/json');
        
        $notificacionModel = $this->model('NotificacionModel');
        $resultado = $notificacionModel->marcarLeida($id);
        
        echo json_encode([
            'success' => $resultado
        ]);
    }
    
    public function marcarTodasLeidas() {
        header('Content-Type: application/json');
        
        $rol = $_SESSION['usuario_rol'];
        $notificacionModel = $this->model('NotificacionModel');
        $resultado = $notificacionModel->marcarTodasLeidas($rol);
        
        echo json_encode([
            'success' => $resultado
        ]);
    }
    
    public function contar() {
        header('Content-Type: application/json');
        
        $rol = $_SESSION['usuario_rol'];
        $notificacionModel = $this->model('NotificacionModel');
        $total = $notificacionModel->contarPendientes($rol);
        
        echo json_encode([
            'success' => true,
            'total' => $total
        ]);
    }
}
?>