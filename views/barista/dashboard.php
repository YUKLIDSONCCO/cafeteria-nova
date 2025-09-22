<div class="row">
    <div class="col-md-12">
        <h2>Dashboard - Barista</h2>
        <p>Bienvenido, <?php echo $_SESSION['usuario_nombre']; ?></p>
        <div id="ultimaActualizacion" class="small text-muted"></div>
    </div>
</div>

<!-- Botón flotante para ingredientes faltantes -->
<a href="<?php echo BASE_URL; ?>barista/ingredientesFaltantes" class="btn btn-ingredientes-faltantes">
    <i class="fas fa-exclamation-triangle"></i>
    Reporte de Ingredientes Faltantes
</a>

<div class="row mt-4" id="contenedorPedidos">
    <!-- Pedidos Confirmados -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5>Pedidos Confirmados</h5>
            </div>
            <div class="card-body" id="pedidos_confirmados">
                <?php if (!empty($pedidos_confirmados)): ?>
                    <?php foreach ($pedidos_confirmados as $pedido): ?>
                    <div class="card mb-2">
                        <div class="card-body">
                            <h6>Pedido #<?php echo $pedido['codigo']; ?></h6>
                            <?php if (!empty($pedido['productos_categorizados'])): ?>
                                <!-- Bebidas -->
                                <?php if (!empty($pedido['productos_categorizados']['bebidas'])): ?>
                                    <div class="productos-seccion bebidas">
                                        <h6 class="productos-titulo">Bebidas</h6>
                                        <?php foreach ($pedido['productos_categorizados']['bebidas'] as $detalle): ?>
                                            <p>
                                                <span class="badge bg-primary"><?php echo $detalle['cantidad']; ?>x</span>
                                                <?php echo $detalle['producto_nombre']; ?>
                                            </p>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Separador -->
                                <?php if (!empty($pedido['productos_categorizados']['bebidas']) && 
                                        !empty($pedido['productos_categorizados']['alimentos'])): ?>
                                    <hr class="productos-separador">
                                <?php endif; ?>

                                <!-- Alimentos -->
                                <?php if (!empty($pedido['productos_categorizados']['alimentos'])): ?>
                                    <div class="productos-seccion alimentos">
                                        <h6 class="productos-titulo">Alimentos</h6>
                                        <?php foreach ($pedido['productos_categorizados']['alimentos'] as $detalle): ?>
                                            <p>
                                                <span class="badge bg-success"><?php echo $detalle['cantidad']; ?>x</span>
                                                <?php echo $detalle['producto_nombre']; ?>
                                            </p>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <p>Total: $<?php echo number_format($pedido['total'], 2); ?></p>
                            <a href="<?php echo BASE_URL; ?>barista/iniciarPreparacion/<?php echo $pedido['id']; ?>" 
                               class="btn btn-sm btn-warning">Iniciar Preparación</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No hay pedidos confirmados</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- En Preparación -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-warning">
                <h5>En Preparación</h5>
            </div>
            <div class="card-body" id="pedidos_preparacion">
                <?php if (!empty($pedidos_preparacion)): ?>
                    <?php foreach ($pedidos_preparacion as $pedido): ?>
                    <div class="card mb-2">
                        <div class="card-body">
                            <h6>Pedido #<?php echo $pedido['codigo']; ?></h6>
                            <?php if (!empty($pedido['productos_categorizados'])): ?>
                                <!-- Bebidas -->
                                <?php if (!empty($pedido['productos_categorizados']['bebidas'])): ?>
                                    <div class="productos-seccion bebidas">
                                        <h6 class="productos-titulo">Bebidas</h6>
                                        <?php foreach ($pedido['productos_categorizados']['bebidas'] as $detalle): ?>
                                            <p>
                                                <span class="badge bg-primary"><?php echo $detalle['cantidad']; ?>x</span>
                                                <?php echo $detalle['producto_nombre']; ?>
                                            </p>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Separador -->
                                <?php if (!empty($pedido['productos_categorizados']['bebidas']) && 
                                        !empty($pedido['productos_categorizados']['alimentos'])): ?>
                                    <hr class="productos-separador">
                                <?php endif; ?>

                                <!-- Alimentos -->
                                <?php if (!empty($pedido['productos_categorizados']['alimentos'])): ?>
                                    <div class="productos-seccion alimentos">
                                        <h6 class="productos-titulo">Alimentos</h6>
                                        <?php foreach ($pedido['productos_categorizados']['alimentos'] as $detalle): ?>
                                            <p>
                                                <span class="badge bg-success"><?php echo $detalle['cantidad']; ?>x</span>
                                                <?php echo $detalle['producto_nombre']; ?>
                                            </p>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <p>Total: $<?php echo number_format($pedido['total'], 2); ?></p>
                            <a href="<?php echo BASE_URL; ?>barista/finalizarPreparacion/<?php echo $pedido['id']; ?>" 
                               class="btn btn-sm btn-success">Marcar como Listo</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No hay pedidos en preparación</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Listos -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5>Listos para Entregar</h5>
            </div>
            <div class="card-body" id="pedidos_listos">
                <?php if (!empty($pedidos_listos)): ?>
                    <?php foreach ($pedidos_listos as $pedido): ?>
                    <div class="card mb-2">
                        <div class="card-body">
                            <h6>Pedido #<?php echo $pedido['codigo']; ?></h6>
                            <?php if (!empty($pedido['productos_categorizados'])): ?>
                                <!-- Bebidas -->
                                <?php if (!empty($pedido['productos_categorizados']['bebidas'])): ?>
                                    <div class="productos-seccion bebidas">
                                        <h6 class="productos-titulo">Bebidas</h6>
                                        <?php foreach ($pedido['productos_categorizados']['bebidas'] as $detalle): ?>
                                            <p>
                                                <span class="badge bg-primary"><?php echo $detalle['cantidad']; ?>x</span>
                                                <?php echo $detalle['producto_nombre']; ?>
                                            </p>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Separador -->
                                <?php if (!empty($pedido['productos_categorizados']['bebidas']) && 
                                        !empty($pedido['productos_categorizados']['alimentos'])): ?>
                                    <hr class="productos-separador">
                                <?php endif; ?>

                                <!-- Alimentos -->
                                <?php if (!empty($pedido['productos_categorizados']['alimentos'])): ?>
                                    <div class="productos-seccion alimentos">
                                        <h6 class="productos-titulo">Alimentos</h6>
                                        <?php foreach ($pedido['productos_categorizados']['alimentos'] as $detalle): ?>
                                            <p>
                                                <span class="badge bg-success"><?php echo $detalle['cantidad']; ?>x</span>
                                                <?php echo $detalle['producto_nombre']; ?>
                                            </p>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <p>Total: $<?php echo number_format($pedido['total'], 2); ?></p>
                            <span class="badge bg-success">Listo</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No hay pedidos listos</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
