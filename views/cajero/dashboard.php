<div class="row">
    <div class="col-md-12">
        <h2>Dashboard - Cajero</h2>
        <p>Bienvenido, <?php echo $_SESSION['usuario_nombre']; ?></p>
    </div>
</div>

<div class="row mt-4">
    <!-- Resumen del Día -->
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h5>Total del Día</h5>
                <h3>$<?php echo number_format($total_dia, 2); ?></h3>
            </div>
        </div>
    </div>

    <!-- Pedidos Listos para Pagar -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-warning">
                <h5>Pedidos Listos para Pagar</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($pedidos_listos)): ?>
                    <?php foreach ($pedidos_listos as $pedido): ?>
                    <div class="card mb-2">
                        <div class="card-body">
                            <h6>Pedido #<?php echo $pedido['codigo']; ?></h6>
                            <p>Total: $<?php echo number_format($pedido['total'], 2); ?></p>
                            <p>Cliente: <?php echo $pedido['cliente_nombre'] ?? 'Cliente no registrado'; ?></p>
                            <a href="<?php echo BASE_URL; ?>cajero/procesarPago/<?php echo $pedido['id']; ?>" 
                               class="btn btn-sm btn-success">Procesar Pago</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No hay pedidos listos para pagar</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Acciones Rápidas</h5>
            </div>
            <div class="card-body">
                <a href="<?php echo BASE_URL; ?>cajero/pagos" class="btn btn-primary me-2">Gestión de Pagos</a>
                <a href="<?php echo BASE_URL; ?>cajero/cierreCaja" class="btn btn-secondary">Cierre de Caja</a>
            </div>
        </div>
    </div>
</div>