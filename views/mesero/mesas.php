<?php
// views/mesero/mesas.php
// Variables esperadas: $mesas (array), BASE_URL constante para rutas
?>
<div class="row">
    <div class="col-md-12">
        <h2>Mesas</h2>
    </div>
</div>

<div class="row mt-3">
    <?php if (!empty($mesas)): ?>
        <?php foreach ($mesas as $mesa): ?>
            <div class="col-md-3 mb-3">
                <div class="card <?php echo ($mesa['estado'] == 'libre') ? 'bg-light' : 'bg-secondary text-white'; ?>">
                    <div class="card-body text-center">
                        <h5><?php echo htmlspecialchars($mesa['codigo']); ?></h5>
                        <p>Capacidad: <?php echo (int)$mesa['capacidad']; ?> pers.</p>
                        <p>Estado: <strong><?php echo ucfirst($mesa['estado']); ?></strong></p>

                        <?php if ($mesa['estado'] == 'libre'): ?>
                            <!-- Form para asignar mesa a un pedido -->
                            <form action="<?php echo BASE_URL; ?>mesero/asignarMesa" method="POST" class="mt-2">
                                <input type="hidden" name="mesa_id" value="<?php echo (int)$mesa['id']; ?>">
                                <div class="input-group">
                                    <input type="number" name="pedido_id" class="form-control" placeholder="ID pedido" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">Asignar</button>
                                    </div>
                                </div>
                            </form>
                        <?php else: ?>
                            <form action="<?php echo BASE_URL; ?>mesero/liberarMesa" method="POST" class="mt-2">
                                <input type="hidden" name="mesa_id" value="<?php echo (int)$mesa['id']; ?>">
                                <button class="btn btn-sm btn-light" type="submit">Liberar mesa</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-md-12">
            <p>No hay mesas configuradas.</p>
        </div>
    <?php endif; ?>
</div>
