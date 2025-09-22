<div class="row">
    <div class="col-md-12 text-center mb-4">
        <h2 class="fw-bold" style="color:#6a0dad;">🌸 PANEL DEL BARISTA 🌸</h2>
        <p class="text-muted">Bienvenido, <?php echo $_SESSION['usuario_nombre']; ?></p>
        <div id="ultimaActualizacion" class="small text-muted"></div>
    </div>
</div>

<!-- Botón flotante para ingredientes faltantes -->
<a href="<?php echo BASE_URL; ?>barista/ingredientesFaltantes" 
   class="btn btn-ingredientes-faltantes shadow-sm" 
   style="background: linear-gradient(45deg, #ff4da6, #d966ff); color:white; border-radius: 30px; padding: 10px 20px; position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
    <i class="fas fa-exclamation-triangle"></i> Reporte de Ingredientes Faltantes
</a>

<div class="row mt-4" id="contenedorPedidos">
    <!-- Pedidos Confirmados -->
    <div class="col-md-4">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header text-white text-center" 
                 style="background: linear-gradient(45deg, #9b59b6, #ff66b2);">
                <h5 class="mb-0">💜 Pedidos Confirmados</h5>
            </div>
            <div class="card-body" id="pedidos_confirmados">
                <?php if (!empty($pedidos_confirmados)): ?>
                    <?php foreach ($pedidos_confirmados as $pedido): ?>
                    <div class="card mb-3 border-0 shadow-sm rounded-4" style="background-color: #f9f1fb;">
                        <div class="card-body">
                            <h6 class="fw-bold" style="color:#8e44ad;">Pedido <?php echo "NV-" . str_pad($pedido['id'], 4, "0", STR_PAD_LEFT); ?></h6>
                            <?php if (!empty($pedido['productos_categorizados'])): ?>
                                <!-- Bebidas -->
                                <?php if (!empty($pedido['productos_categorizados']['bebidas'])): ?>
                                    <div class="productos-seccion bebidas mb-2">
                                        <h6 class="productos-titulo text-primary">🥤 Bebidas</h6>
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
                                        <h6 class="productos-titulo text-success">🍰 Alimentos</h6>
                                        <?php foreach ($pedido['productos_categorizados']['alimentos'] as $detalle): ?>
                                            <p>
                                                <span class="badge bg-success"><?php echo $detalle['cantidad']; ?>x</span>
                                                <?php echo $detalle['producto_nombre']; ?>
                                            </p>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <p class="fw-bold mt-2" style="color:#d63384;">Total: $<?php echo number_format($pedido['total'], 2); ?></p>
                            <a href="<?php echo BASE_URL; ?>barista/iniciarPreparacion/<?php echo $pedido['id']; ?>" 
                               class="btn btn-sm text-white fw-bold" 
                               style="background: linear-gradient(45deg, #f39c12, #ff6699); border-radius: 20px;">Iniciar Preparación</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted text-center">✨ No hay pedidos confirmados ✨</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- En Preparación -->
    <div class="col-md-4">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header text-dark text-center" 
                 style="background: linear-gradient(45deg, #f1c40f, #ff99cc);">
                <h5 class="mb-0">💛 En Preparación</h5>
            </div>
            <div class="card-body" id="pedidos_preparacion">
                <?php if (!empty($pedidos_preparacion)): ?>
                    <?php foreach ($pedidos_preparacion as $pedido): ?>
                    <div class="card mb-3 border-0 shadow-sm rounded-4" style="background-color: #fff7e6;">
                        <div class="card-body">
                            <h6 class="fw-bold" style="color:#e67e22;">Pedido <?php echo "NV-" . str_pad($pedido['id'], 4, "0", STR_PAD_LEFT); ?></h6>
                            <?php if (!empty($pedido['productos_categorizados'])): ?>
                                <!-- Bebidas -->
                                <?php if (!empty($pedido['productos_categorizados']['bebidas'])): ?>
                                    <div class="productos-seccion bebidas mb-2">
                                        <h6 class="productos-titulo text-primary">🥤 Bebidas</h6>
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
                                        <h6 class="productos-titulo text-success">🍰 Alimentos</h6>
                                        <?php foreach ($pedido['productos_categorizados']['alimentos'] as $detalle): ?>
                                            <p>
                                                <span class="badge bg-success"><?php echo $detalle['cantidad']; ?>x</span>
                                                <?php echo $detalle['producto_nombre']; ?>
                                            </p>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <p class="fw-bold mt-2" style="color:#d63384;">Total: $<?php echo number_format($pedido['total'], 2); ?></p>
                            <a href="<?php echo BASE_URL; ?>barista/finalizarPreparacion/<?php echo $pedido['id']; ?>" 
                               class="btn btn-sm text-white fw-bold" 
                               style="background: linear-gradient(45deg, #27ae60, #ff66b2); border-radius: 20px;">Marcar como Listo</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted text-center">✨ No hay pedidos en preparación ✨</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Listos -->
    <div class="col-md-4">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header text-white text-center" 
                 style="background: linear-gradient(45deg, #2ecc71, #ff99cc);">
                <h5 class="mb-0">💚 Listos para Entregar</h5>
            </div>
            <div class="card-body" id="pedidos_listos">
                <?php if (!empty($pedidos_listos)): ?>
                    <?php foreach ($pedidos_listos as $pedido): ?>
                    <div class="card mb-3 border-0 shadow-sm rounded-4" style="background-color: #f0fff4;">
                        <div class="card-body">
                            <h6 class="fw-bold" style="color:#27ae60;">Pedido <?php echo "NV-" . str_pad($pedido['id'], 4, "0", STR_PAD_LEFT); ?></h6>
                            <?php if (!empty($pedido['productos_categorizados'])): ?>
                                <!-- Bebidas -->
                                <?php if (!empty($pedido['productos_categorizados']['bebidas'])): ?>
                                    <div class="productos-seccion bebidas mb-2">
                                        <h6 class="productos-titulo text-primary">🥤 Bebidas</h6>
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
                                        <h6 class="productos-titulo text-success">🍰 Alimentos</h6>
                                        <?php foreach ($pedido['productos_categorizados']['alimentos'] as $detalle): ?>
                                            <p>
                                                <span class="badge bg-success"><?php echo $detalle['cantidad']; ?>x</span>
                                                <?php echo $detalle['producto_nombre']; ?>
                                            </p>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <p class="fw-bold mt-2" style="color:#d63384;">Total: $<?php echo number_format($pedido['total'], 2); ?></p>
                            <span class="badge" 
                                  style="background: linear-gradient(45deg, #2ecc71, #ff66b2); font-size: 14px; padding: 8px 12px; border-radius: 15px;">✨ Listo ✨</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted text-center">✨ No hay pedidos listos ✨</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
