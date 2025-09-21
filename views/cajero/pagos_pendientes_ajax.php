<?php
require_once '../../models/PagoModel.php';
$pagoModel = new PagoModel();
$pendientes = $pagoModel->obtenerPagosPendientes();
header('Content-Type: application/json');
echo json_encode(is_array($pendientes) ? $pendientes : []);
