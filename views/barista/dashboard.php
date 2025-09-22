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
                    <div class="card mb-3 border-0 shadow-sm rounded-4 pedido-card cursor-pointer hover-effect" 
                         data-pedido-id="<?php echo $pedido['id']; ?>" 
                         data-estado="confirmado"
                         style="background-color: #f9f1fb; transition: all 0.3s ease;">
                        <div class="card-body">
                            <h6 class="fw-bold" style="color:#8e44ad;">
                                Pedido <?php echo "NV-" . str_pad($pedido['id'], 4, "0", STR_PAD_LEFT); ?>
                                <span class="click-indicator">👆</span>
                            </h6>
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
                            <small class="text-muted">👆 Click para iniciar preparación</small>
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
                    <div class="card mb-3 border-0 shadow-sm rounded-4 pedido-card cursor-pointer hover-effect" 
                         data-pedido-id="<?php echo $pedido['id']; ?>" 
                         data-estado="preparacion"
                         style="background-color: #fff7e6; transition: all 0.3s ease;">
                        <div class="card-body">
                            <h6 class="fw-bold" style="color:#e67e22;">
                                Pedido <?php echo "NV-" . str_pad($pedido['id'], 4, "0", STR_PAD_LEFT); ?>
                                <span class="click-indicator">👆</span>
                            </h6>
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
                            <small class="text-muted">👆 Click para marcar como listo</small>
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

<!-- CSS adicional para efectos del Dashboard Barista -->
<style>
.cursor-pointer {
    cursor: pointer !important;
}

.hover-effect {
    transition: all 0.3s ease !important;
}

.hover-effect:hover {
    transform: translateY(-3px) !important;
    box-shadow: 0 8px 25px rgba(106, 13, 173, 0.2) !important;
}

.click-indicator {
    font-size: 11px;
    margin-left: 8px;
    animation: bounce 1.5s infinite;
    color: #6a0dad;
}

.processing {
    pointer-events: none !important;
    opacity: 0.6 !important;
}

.pedido-card {
    position: relative;
    overflow: hidden;
}

.pedido-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, transparent, #6a0dad, transparent);
    transition: left 0.5s;
}

.pedido-card:hover::before {
    left: 100%;
}

#ultimaActualizacion {
    font-size: 11px;
    color: #6c757d;
    background: rgba(106, 13, 173, 0.1);
    padding: 4px 12px;
    border-radius: 15px;
    display: inline-block;
    margin-top: 5px;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-3px); }
    60% { transform: translateY(-1px); }
}

@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes slideOut {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(100%); opacity: 0; }
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(106, 13, 173, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(106, 13, 173, 0); }
    100% { box-shadow: 0 0 0 0 rgba(106, 13, 173, 0); }
}

.alert {
    animation: pulse 2s infinite;
}
</style>

<!-- JavaScript del Dashboard del Barista -->
<script src="<?php echo BASE_URL; ?>public/js/barista-dashboard.js"></script>
