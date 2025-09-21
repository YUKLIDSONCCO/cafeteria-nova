<?php
// Este archivo parece ser una vista, así que asumo que las variables vienen del controlador
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Reporte de Ventas</h2>
        <a href="<?php echo BASE_URL; ?>cajero/dashboard" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Regresar al Dashboard
        </a>
    </div>

    <form method="get" class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">Fecha Inicio</label>
            <input type="date" name="inicio" value="<?php echo $fecha_inicio; ?>" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Fecha Fin</label>
            <input type="date" name="fin" value="<?php echo $fecha_fin; ?>" class="form-control">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>N° Pedidos</th>
                    <th>Total Ventas</th>
                    <th>Promedio Pedido</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reporte as $fila): ?>
                <tr>
                    <td><?php echo $fila['fecha']; ?></td>
                    <td><?php echo $fila['numero_pedidos']; ?></td>
                    <td>$<?php echo number_format($fila['total_ventas'], 2); ?></td>
                    <td>$<?php echo number_format($fila['promedio_pedido'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
