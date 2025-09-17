<?php 
// Verifica la ruta correcta del header
$headerPath = dirname(__DIR__) . '/templates/header.php';
if (file_exists($headerPath)) {
    require_once $headerPath;
} else {
    // Fallback si no encuentra el header
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Inventario - Admin</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"></head><body>';
}
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4"><?php echo $titulo; ?></h2>
            
            <!-- Resumen del inventario -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h5>Valor Total del Inventario</h5>
                            <h3>$<?php echo number_format($total_valor_inventario, 2); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-dark">
                        <div class="card-body text-center">
                            <h5>Productos con Stock Bajo</h5>
                            <h3><?php echo count($productos_bajos); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h5>Total de Productos</h5>
                            <h3><?php echo count($inventario); ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alertas de stock bajo -->
            <?php if (!empty($productos_bajos)): ?>
            <div class="alert alert-warning mb-4">
                <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Alertas de Stock Bajo</h5>
                <div class="row">
                    <?php foreach ($productos_bajos as $producto): ?>
                    <div class="col-md-4 mb-2">
                        <strong><?php echo htmlspecialchars($producto['producto']); ?></strong>: 
                        Stock: <?php echo $producto['cantidad_actual']; ?> 
                        (Mínimo: <?php echo $producto['minimo']; ?>)
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Barra de acciones -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gestión de Inventario</h5>
                    <div>
                        <form method="POST" class="d-inline me-2">
                            <input type="hidden" name="action" value="generar_alerta">
                            <button type="submit" class="btn btn-warning btn-sm">
                                <i class="fas fa-bell"></i> Generar Alertas
                            </button>
                        </form>
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#agregarProductoModal">
                            <i class="fas fa-plus"></i> Agregar Producto
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabla de inventario -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Productos en Inventario</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($inventario)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Stock Actual</th>
                                        <th>Stock Mínimo</th>
                                        <th>Estado</th>
                                        <th>Precio Unitario</th>
                                        <th>Valor Total</th>
                                        <th>Última Actualización</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($inventario as $item): ?>
                                        <tr class="<?php echo $item['cantidad_actual'] <= $item['minimo'] ? 'table-warning' : ''; ?>">
                                            <td>
                                                <strong><?php echo htmlspecialchars($item['producto']); ?></strong>
                                                <?php if (!empty($item['categoria'])): ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($item['categoria']); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $item['cantidad_actual']; ?></td>
                                            <td><?php echo $item['minimo']; ?></td>
                                            <td>
                                                <?php if ($item['cantidad_actual'] <= $item['minimo']): ?>
                                                    <span class="badge bg-warning">Stock Bajo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Normal</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>$<?php echo number_format($item['precio'] ?? 0, 2); ?></td>
                                            <td>$<?php echo number_format(($item['cantidad_actual'] * ($item['precio'] ?? 0)), 2); ?></td>
                                            <td><?php echo !empty($item['actualizado_en']) ? date('d/m/Y H:i', strtotime($item['actualizado_en'])) : 'N/A'; ?></td>
                                            <td>
                                                <button class="btn btn-primary btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editarStockModal"
                                                        data-id="<?php echo $item['id']; ?>"
                                                        data-producto="<?php echo htmlspecialchars($item['producto']); ?>"
                                                        data-cantidad="<?php echo $item['cantidad_actual']; ?>"
                                                        data-minimo="<?php echo $item['minimo']; ?>">
                                                    <i class="fas fa-edit"></i> Editar
                                                </button>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="action" value="eliminar">
                                                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm" 
                                                            onclick="return confirm('¿Está seguro de eliminar este producto del inventario?')">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            No hay productos en el inventario.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar stock -->
<div class="modal fade" id="editarStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="actualizar">
                <input type="hidden" name="producto_id" id="editProductoId">
                
                <div class="modal-header">
                    <h5 class="modal-title">Editar Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Producto</label>
                        <input type="text" class="form-control" id="editProductoNombre" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock Actual</label>
                        <input type="number" class="form-control" name="cantidad" id="editCantidad" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock Mínimo</label>
                        <input type="number" class="form-control" name="minimo" id="editMinimo" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para agregar producto -->
<div class="modal fade" id="agregarProductoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="agregar">
                
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Producto al Inventario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre del Producto</label>
                        <input type="text" class="form-control" name="producto" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock Inicial</label>
                        <input type="number" class="form-control" name="cantidad_actual" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock Mínimo</label>
                        <input type="number" class="form-control" name="minimo" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Agregar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Script para el modal de edición
document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('editarStockModal');
    editModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const producto = button.getAttribute('data-producto');
        const cantidad = button.getAttribute('data-cantidad');
        const minimo = button.getAttribute('data-minimo');
        
        document.getElementById('editProductoId').value = id;
        document.getElementById('editProductoNombre').value = producto;
        document.getElementById('editCantidad').value = cantidad;
        document.getElementById('editMinimo').value = minimo;
    });
});
</script>

<?php 
// Verifica la ruta correcta del footer
$footerPath = dirname(__DIR__) . '/templates/footer.php';
if (file_exists($footerPath)) {
    require_once $footerPath;
} else {
    // Fallback si no encuentra el footer
    echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script></body></html>';
}
?>