<div class="row">
    <div class="col-md-12 mb-3">
        <div class="p-3 rounded shadow-sm" style="background: linear-gradient(90deg, #e1bee7, #f8bbd0); color:#4a148c;">
            <h2 class="fw-bold">✨ PANEL DEL MESERO ✨</h2>
            <p class="mb-0">Bienvenido, <strong><?php echo $_SESSION['usuario_nombre']; ?></strong></p>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Pedidos Pendientes -->
    <div class="col-md-6">
        <div class="card shadow-lg border-0" style="border-radius: 18px;">
            <div class="card-header text-white fw-bold" style="background: linear-gradient(45deg, #f48fb1, #ce93d8); border-radius:18px 18px 0 0;">
                <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Pedidos Pendientes</h5>
            </div>
            <div class="card-body" style="background: #fdfdfe;">
                <?php if (!empty($pedidos_pendientes)): ?>
                    <?php foreach ($pedidos_pendientes as $pedido): ?>
                        <div class="card mb-3 border-0 shadow-sm" style="border-radius:14px;">
                            <div class="card-body" style="background: linear-gradient(135deg, #f3e5f5, #fce4ec); border-radius:14px;">
                                <h6 class="fw-bold text-dark">Pedido #<?php echo "NV-" . str_pad($pedido['id'], 4, "0", STR_PAD_LEFT); ?></h6>
                                <p class="mb-1">💰 <strong>Total:</strong> $<?php echo number_format($pedido['total'], 2); ?></p>
                                <p class="mb-2">📌 <strong>Tipo:</strong> <?php echo ucfirst($pedido['tipo']); ?></p>
                                <a href="<?php echo BASE_URL; ?>mesero/confirmarPedido/<?php echo $pedido['id']; ?>" 
                                   class="btn btn-sm text-white fw-bold" style="background:#8e24aa; border-radius:12px;">Confirmar</a>
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
    <div class="col-md-6">
        <div class="card shadow-lg border-0" style="border-radius: 18px;">
            <div class="card-header text-white fw-bold" style="background: linear-gradient(45deg, #ba68c8, #f48fb1); border-radius:18px 18px 0 0;">
                <h5 class="mb-0"><i class="fas fa-chair me-2"></i>Estado de Mesas</h5>
            </div>
            <div class="card-body" style="background: #fdfdfe;">
                <div class="row">
                    <?php foreach ($mesas as $mesa): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card text-white border-0 shadow-sm" 
                                 style="border-radius:16px; 
                                        background: <?php echo $mesa['estado'] === 'libre' 
                                            ? 'linear-gradient(135deg,#81c784,#aed581)' 
                                            : 'linear-gradient(135deg,#e57373,#f06292)'; ?>;">
                                <div class="card-body text-center">
                                    <h6 class="fw-bold">Mesa <?php echo $mesa['codigo']; ?></h6>
                                    <p class="mb-1"><?php echo ucfirst($mesa['estado']); ?></p>
                                    <small>👥 Cap: <?php echo $mesa['capacidad']; ?> pers.</small>
                                    <br>

                                    <?php if ($mesa['estado'] === 'libre'): ?>
                                        <!-- Formulario para asignar pedido -->
                                        <form action="<?php echo BASE_URL; ?>mesero/asignarMesa" method="POST" class="mt-2">
                                            <input type="hidden" name="mesa_id" value="<?php echo $mesa['id']; ?>">
                                            
                                            <select name="pedido_id" class="form-control form-control-sm mb-2 rounded-pill" required>
                                                <option value="">Seleccionar pedido</option>
                                                <?php foreach ($pedidos_pendientes as $pedido): ?>
                                                    <option value="<?php echo $pedido['id']; ?>">
                                                        Pedido #<?php echo "NV-" . str_pad($pedido['id'], 4, "0", STR_PAD_LEFT); ?> 
                                                        (S/ <?php echo number_format($pedido['total'], 2); ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>

                                            <button type="submit" class="btn btn-light btn-sm fw-bold rounded-pill">Asignar</button>
                                        </form>
                                    <?php else: ?>
                                        <!-- Botón Liberar -->
                                        <form action="<?php echo BASE_URL; ?>mesero/liberarMesa" method="POST" class="mt-2">
                                            <input type="hidden" name="mesa_id" value="<?php echo $mesa['id']; ?>">
                                            <button type="submit" class="btn btn-dark btn-sm fw-bold rounded-pill">Liberar</button>
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
