<div class="row">
    <div class="col-md-12">
        <h2>Dashboard - Mesero</h2>
        <p>Bienvenido, <?php echo $_SESSION['usuario_nombre']; ?></p>
    </div>
</div>

<div class="row mt-4">
    <!-- Pedidos Pendientes -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning">
                <h5>Pedidos Pendientes</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($pedidos_pendientes)): ?>
                    <?php foreach ($pedidos_pendientes as $pedido): ?>
                    <div class="card mb-2">
                        <div class="card-body">
                            <h6>Pedido #<?php echo $pedido['codigo']; ?></h6>
                            <p>Total: $<?php echo number_format($pedido['total'], 2); ?></p>
                            <p>Tipo: <?php echo ucfirst($pedido['tipo']); ?></p>
                            <a href="<?php echo BASE_URL; ?>mesero/confirmarPedido/<?php echo $pedido['id']; ?>" 
                               class="btn btn-sm btn-success">Confirmar</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No hay pedidos pendientes</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Mesas -->
<!-- Mesas -->
<div class="col-md-6">
    <div class="card">
        <div class="card-header bg-info">
            <h5>Estado de Mesas</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($mesas as $mesa): ?>

                    <div class="col-md-6 mb-3">
                        <div class="card 
                            <?php echo $mesa['estado'] === 'libre' ? 'bg-success' : 'bg-danger'; ?> 
                            text-white">
                            <div class="card-body text-center">
                                <h6>Mesa <?php echo $mesa['codigo']; ?></h6>
                                <p><?php echo ucfirst($mesa['estado']); ?></p>
                                <small>Cap: <?php echo $mesa['capacidad']; ?> pers.</small>
                                <br>

                                <?php if ($mesa['estado'] === 'libre'): ?>
                                    <!-- Formulario para asignar pedido -->
                                    <form action="<?php echo BASE_URL; ?>mesero/asignarMesa" method="POST" class="mt-2">
                                        <input type="hidden" name="mesa_id" value="<?php echo $mesa['id']; ?>">
                                        
                                        <select name="pedido_id" class="form-control form-control-sm mb-2" required>
                                            <option value="">Seleccionar pedido</option>
                                            <?php foreach ($pedidos_pendientes as $pedido): ?>
                                                <option value="<?php echo $pedido['id']; ?>">
                                                    Pedido #<?php echo $pedido['codigo']; ?> (S/ <?php echo number_format($pedido['total'], 2); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <button type="submit" class="btn btn-light btn-sm">Asignar</button>
                                    </form>
                                <?php else: ?>
                                    <!-- Botón Liberar -->
                                    <form action="<?php echo BASE_URL; ?>mesero/liberarMesa" method="POST" class="mt-2">
                                        <input type="hidden" name="mesa_id" value="<?php echo $mesa['id']; ?>">
                                        <button type="submit" class="btn btn-dark btn-sm">Liberar</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

</div>