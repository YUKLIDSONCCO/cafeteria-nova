<?php 
// Verifica la ruta correcta del header
$headerPath = dirname(__DIR__) . '/templates/header.php';
if (file_exists($headerPath)) {
    require_once $headerPath;
} else {
    // Fallback si no encuentra el header
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Reportes - Admin</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"></head><body>';
}
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4"><?php echo $titulo; ?></h2>
            
            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Filtros de Reporte</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="" class="row g-3">
                        <div class="col-md-3">
                            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                                   value="<?php echo $fecha_inicio; ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="fecha_fin" class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                                   value="<?php echo $fecha_fin; ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="tipo_reporte" class="form-label">Tipo de Reporte</label>
                            <select class="form-select" id="tipo_reporte" name="tipo_reporte">
                                <?php foreach ($tipos_reporte as $key => $value): ?>
                                    <option value="<?php echo $key; ?>" 
                                        <?php echo $tipo_reporte == $key ? 'selected' : ''; ?>>
                                        <?php echo $value; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">Generar Reporte</button>
                            <button type="submit" name="exportar" value="1" class="btn btn-success">
                                <i class="fas fa-download"></i> Exportar CSV
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Resultados del Reporte -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        Reporte de <?php echo $tipos_reporte[$tipo_reporte]; ?> 
                        (<?php echo date('d/m/Y', strtotime($fecha_inicio)); ?> - <?php echo date('d/m/Y', strtotime($fecha_fin)); ?>)
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($reporte_data)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <?php if ($tipo_reporte == 'ventas'): ?>
                                            <th>Fecha</th>
                                            <th>N° Pedidos</th>
                                            <th>Total Ventas</th>
                                            <th>Promedio por Pedido</th>
                                        <?php elseif ($tipo_reporte == 'productos'): ?>
                                            <th>Producto</th>
                                            <th>Categoría</th>
                                            <th>Cantidad Vendida</th>
                                            <th>Total Vendido</th>
                                        <?php elseif ($tipo_reporte == 'usuarios'): ?>
                                            <th>Usuario</th>
                                            <th>Email</th>
                                            <th>Pedidos Realizados</th>
                                            <th>Total Gastado</th>
                                        <?php elseif ($tipo_reporte == 'inventario'): ?>
                                            <th>Producto</th>
                                            <th>Categoría</th>
                                            <th>Stock Actual</th>
                                            <th>Precio Unitario</th>
                                            <th>Valor Total</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reporte_data as $fila): ?>
                                        <tr>
                                            <?php if ($tipo_reporte == 'ventas'): ?>
                                                <td><?php echo date('d/m/Y', strtotime($fila['fecha'])); ?></td>
                                                <td><?php echo $fila['numero_pedidos']; ?></td>
                                                <td>$<?php echo number_format($fila['total_ventas'], 2); ?></td>
                                                <td>$<?php echo number_format($fila['promedio_pedido'], 2); ?></td>
                                            <?php elseif ($tipo_reporte == 'productos'): ?>
                                                <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                                                <td><?php echo htmlspecialchars($fila['categoria']); ?></td>
                                                <td><?php echo $fila['cantidad_vendida']; ?></td>
                                                <td>$<?php echo number_format($fila['total_vendido'], 2); ?></td>
                                            <?php elseif ($tipo_reporte == 'usuarios'): ?>
                                                <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                                                <td><?php echo htmlspecialchars($fila['email']); ?></td>
                                                <td><?php echo $fila['pedidos_realizados']; ?></td>
                                                <td>$<?php echo number_format($fila['total_gastado'], 2); ?></td>
                                            <?php elseif ($tipo_reporte == 'inventario'): ?>
                                                <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                                                <td><?php echo htmlspecialchars($fila['categoria']); ?></td>
                                                <td><?php echo $fila['stock']; ?></td>
                                                <td>$<?php echo number_format($fila['precio'], 2); ?></td>
                                                <td>$<?php echo number_format($fila['valor_total'], 2); ?></td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <?php if ($tipo_reporte == 'ventas' && !empty($reporte_data)): ?>
                                    <tfoot class="table-info">
                                        <tr>
                                            <th>TOTALES</th>
                                            <th><?php echo array_sum(array_column($reporte_data, 'numero_pedidos')); ?></th>
                                            <th>$<?php echo number_format(array_sum(array_column($reporte_data, 'total_ventas')), 2); ?></th>
                                            <th>$<?php echo number_format(array_sum(array_column($reporte_data, 'total_ventas')) / array_sum(array_column($reporte_data, 'numero_pedidos')), 2); ?></th>
                                        </tr>
                                    </tfoot>
                                <?php endif; ?>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            No hay datos disponibles para el reporte seleccionado en el rango de fechas especificado.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Verifica la ruta correcta del footer
$footerPath = dirname(__DIR__) . '/templates/footer.php';
if (file_exists($footerPath)) {
    require_once $footerPath;
} else {
    // Fallback si no encuentra el footer
    echo '</body></html>';
}
?>