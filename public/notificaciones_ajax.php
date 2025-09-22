<?php
// Cargar configuración de la base de datos
require_once __DIR__ . '/../config/config.php';
// Luego la clase Database
require_once __DIR__ . '/../config/database.php';


session_start();

// Verificar sesión
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'cajero') {
    header('Content-Type: application/json');
    echo json_encode(['nuevas' => 0]);
    exit;
}

require_once __DIR__ . '/../models/PedidoModel.php';

$pedidoModel = new PedidoModel();
$pendientes = $pedidoModel->obtenerPedidosParaPago();

header('Content-Type: application/json');
echo json_encode(['nuevas' => is_array($pendientes) ? count($pendientes) : 0]);
?>
