<?php
// Mostrar el primer pedido pendiente de pago
defined('BASE_URL') or define('BASE_URL', '/');
if (!empty($pedidos_pendientes)) {
    $pedido = $pedidos_pendientes[0];
    $pedido_id = $pedido['id'];
    $cliente = $pedido['cliente_nombre'] ?? 'Cliente';
    $tipo = ucfirst($pedido['tipo']);
    $estado = ucfirst($pedido['estado']);
    $total = $pedido['total'];

    // Obtener detalles del pedido
    require_once __DIR__ . '/../../models/PedidoModel.php';
    $pedidoModel = new PedidoModel();
    $detalles = $pedidoModel->obtenerDetallesPedido($pedido_id);
?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Procesar Pago - Orden #<?php echo htmlspecialchars($pedido['codigo']); ?></h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Cliente</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($cliente); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Items del Pedido</label>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
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
                                <tr>
                                    <th colspan="3">Subtotal</th>
                                    <th>$<?php echo number_format($total, 2); ?></th>
                                </tr>
                                <tr>
                                    <th colspan="3">Impuestos (10%)</th>
                                    <th>$<?php echo number_format($total * 0.10, 2); ?></th>
                                </tr>
                                <tr>
                                    <th colspan="3">Total</th>
                                    <th>$<?php echo number_format($total * 1.10, 2); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <form method="post" action="<?php echo BASE_URL . 'cajero/procesarPago/' . $pedido_id; ?>">
                        <div class="mb-3">
                            <label class="form-label">Método de Pago</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metodo" id="efectivo" value="efectivo" checked>
                                    <label class="form-check-label" for="efectivo">Efectivo</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metodo" id="tarjeta" value="tarjeta">
                                    <label class="form-check-label" for="tarjeta">Tarjeta</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Monto Recibido</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="monto" id="monto-recibido" placeholder="0.00" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cambio</label>
                            <input type="text" class="form-control" id="cambio" value="$0.00" readonly>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check-circle me-2"></i>Confirmar Pago
                            </button>
                            <a href="<?php echo BASE_URL; ?>cajero/pagos" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
// Calcular cambio automáticamente
document.addEventListener('DOMContentLoaded', function() {
    const montoInput = document.getElementById('monto-recibido');
    const cambioInput = document.getElementById('cambio');
    const total = <?php echo json_encode(round($total * 1.10, 2)); ?>;
    const metodoRadios = document.querySelectorAll('input[name="metodo"]');

    function actualizarCambio() {
        const metodo = document.querySelector('input[name="metodo"]:checked').value;
        let recibido = parseFloat(montoInput.value) || 0;
        if (metodo === 'tarjeta') {
            recibido = total;
            montoInput.value = total;
            montoInput.readOnly = true;
        } else {
            montoInput.readOnly = false;
        }
        const cambio = recibido - total;
        if (cambio >= 0) {
            cambioInput.value = '$' + cambio.toFixed(2);
        } else {
            cambioInput.value = 'Fondos insuficientes';
        }
    }
    montoInput.addEventListener('input', actualizarCambio);
    metodoRadios.forEach(radio => radio.addEventListener('change', actualizarCambio));
});
</script>
<?php } else { ?>
<div class="alert alert-info mt-4 text-center">No hay pedidos pendientes de pago.</div>
<?php } ?>
