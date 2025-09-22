<?php
// views/mesero/pedidos.php
// Variables esperadas: $pedidos (array), BASE_URL
?>
<div class="row">
    <div class="col-md-12">
        <h2>Pedidos</h2>
    </div>
</div>

<div class="row mt-3">
    <?php if (!empty($pedidos)): ?>
        <?php foreach ($pedidos as $pedido): ?>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5>
                            Pedido <?php echo "NV-" . str_pad($pedido['id'], 4, "0", STR_PAD_LEFT); ?>
                            <small class="text-muted">#<?php echo (int)$pedido['id']; ?></small>
                        </h5>
                        <p><strong>Cliente:</strong> <?php echo htmlspecialchars($pedido['cliente_nombre'] ?? 'Cliente'); ?></p>
                        <p><strong>Tipo:</strong> <?php echo ucfirst($pedido['tipo']); ?></p>
                        <p><strong>Total:</strong> S/ <?php echo number_format($pedido['total'], 2); ?></p>
                        <p><strong>Estado:</strong> <span class="badge badge-info"><?php echo ucfirst($pedido['estado']); ?></span></p>
                        <p class="small text-muted">Creado: <?php echo $pedido['creado_en']; ?></p>

                        <div class="btn-group" role="group">
                            <?php if ($pedido['estado'] == 'creado'): ?>
                                <a href="<?php echo BASE_URL; ?>mesero/confirmarPedido/<?php echo (int)$pedido['id']; ?>" class="btn btn-sm btn-success">Confirmar</a>
                            <?php endif; ?>

                            <?php if ($pedido['estado'] == 'confirmado'): ?>
                                <form action="<?php echo BASE_URL; ?>barista/enviarPreparacion" method="POST" style="display:inline;">
                                    <input type="hidden" name="pedido_id" value="<?php echo (int)$pedido['id']; ?>">
                                    <button class="btn btn-sm btn-warning" type="submit">Enviar a Barista</button>
                                </form>
                            <?php endif; ?>

                            <?php if (in_array($pedido['estado'], ['listo','preparacion'])): ?>
                                <form action="<?php echo BASE_URL; ?>mesero/entregarPedido" method="POST" style="display:inline;">
                                    <input type="hidden" name="pedido_id" value="<?php echo (int)$pedido['id']; ?>">
                                    <button class="btn btn-sm btn-primary" type="submit">Marcar Entregado</button>
                                </form>
                            <?php endif; ?>

                            <a href="<?php echo BASE_URL; ?>mesero/verPedido/<?php echo (int)$pedido['id']; ?>" class="btn btn-sm btn-secondary">Ver</a>
                        </div>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-md-12">
            <p>No hay pedidos.</p>
        </div>
    <?php endif; ?>
</div>
