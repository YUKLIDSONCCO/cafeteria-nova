<?php
require_once '../../models/PedidoModel.php';

$fecha_inicio = $_GET['inicio'] ?? date('Y-m-d', strtotime('-6 days'));
$fecha_fin = $_GET['fin'] ?? date('Y-m-d');

$pedidoModel = new PedidoModel();
$reporte = $pedidoModel->obtenerReporteVentas($fecha_inicio, $fecha_fin);

header('Content-Type: application/json');
echo json_encode($reporte);
?>
