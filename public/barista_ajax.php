<?php
session_start();

// Incluir archivos necesarios
require_once '../config/config.php';
require_once '../controllers/BaristaController.php';

// Verificar que sea una petición AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    // Si no es AJAX, permitir también peticiones POST normales
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('HTTP/1.0 400 Bad Request');
        echo json_encode(['error' => 'Solo se permiten peticiones AJAX o POST']);
        exit;
    }
}

// Verificar que el usuario tenga rol de barista
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'barista') {
    header('HTTP/1.0 403 Forbidden');
    echo json_encode(['error' => 'Acceso denegado']);
    exit;
}

try {
    $controller = new BaristaController();
    
    // Obtener la acción de la URL
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'obtenerPedidos':
            $controller->obtenerPedidosJSON();
            break;
            
        case 'iniciarPreparacion':
            $controller->iniciarPreparacionAjax();
            break;
            
        case 'finalizarPreparacion':
            $controller->finalizarPreparacionAjax();
            break;
            
        default:
            header('HTTP/1.0 404 Not Found');
            echo json_encode(['error' => 'Acción no encontrada']);
            break;
    }
    
} catch (Exception $e) {
    header('HTTP/1.0 500 Internal Server Error');
    echo json_encode(['error' => 'Error interno del servidor: ' . $e->getMessage()]);
}
?>