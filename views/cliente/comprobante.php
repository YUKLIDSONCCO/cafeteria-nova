<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header text-center">
                <h3>¡Pedido Confirmado!</h3>
                <p class="mb-0">Cafetería Nova</p>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <h4>
                        N° Pedido: <?php echo "NV-" . str_pad($pedido['id'], 4, "0", STR_PAD_LEFT); ?>
                    </h4>
                    <p class="text-muted"><?php echo date('d/m/Y H:i', strtotime($pedido['creado_en'])); ?></p>
                </div>
                
                <h5>Detalles del Pedido:</h5>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cant</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalles as $detalle): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($detalle['producto_nombre']); ?></td>
                            <td><?php echo $detalle['cantidad']; ?></td>
                            <td>$<?php echo number_format($detalle['precio_unit'], 2); ?></td>
                            <td>$<?php echo number_format($detalle['cantidad'] * $detalle['precio_unit'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-active">
                            <th colspan="3">Total</th>
                            <th>$<?php echo number_format($pedido['total'], 2); ?></th>
                        </tr>
                    </tfoot>
                </table>
                
                <div class="alert alert-info">
                    <strong>Tipo:</strong> <?php echo ucfirst($pedido['tipo']); ?><br>
                    
                    <!-- 🔹 Nombre del cliente (solo si existe) -->
                    <?php if (!empty($pedido['nombre_cliente'])): ?>
                        <strong>Cliente:</strong> <?php echo htmlspecialchars($pedido['nombre_cliente']); ?><br>
                    <?php endif; ?>

                    <strong>Estado:</strong> <span class="badge bg-warning"><?php echo ucfirst($pedido['estado']); ?></span>
                </div>
                
                <div class="d-grid gap-2">
                    <a href="<?php echo BASE_URL; ?>cliente" class="btn btn-primary">Hacer otro pedido</a>
                    <button onclick="window.print()" class="btn btn-outline-secondary">Imprimir Comprobante</button>
                </div>
            </div>
        </div>
    </div>
</div>
