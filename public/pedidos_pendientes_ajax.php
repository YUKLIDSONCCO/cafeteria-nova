<?php
// Cargar configuración de la base de datos
require_once __DIR__ . '/../config/config.php';
// Luego la clase Database
require_once __DIR__ . '/../config/database.php';


// Habilitar errores temporalmente para debug
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();

// Verificar sesión y rol
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'cajero') {
    header('Content-Type: application/json');
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit;
}

require_once __DIR__ . '/../models/PedidoModel.php';

try {
    $pedidoModel = new PedidoModel();
    $pendientes = $pedidoModel->obtenerPedidosParaPago();
    
    if (!is_array($pendientes)) {
        $pendientes = [];
    }
    
    header('Content-Type: application/json');
    echo json_encode($pendientes);
    
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'Error del servidor']);
}
?>
