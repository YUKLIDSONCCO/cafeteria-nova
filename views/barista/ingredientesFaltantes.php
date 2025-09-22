<?php
// Verificar si hay mensajes de éxito o error
if (isset($_SESSION['mensaje'])) {
    echo '<div class="alert alert-' . $_SESSION['mensaje_tipo'] . ' alert-dismissible fade show" role="alert">
            ' . $_SESSION['mensaje'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
    unset($_SESSION['mensaje']);
    unset($_SESSION['mensaje_tipo']);
}
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Reporte de Ingredientes Faltantes</h2>
                <div>
                    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#agregarProductoModal">
                        <i class="fas fa-plus"></i> Agregar Producto
                    </button>
                    <a href="<?php echo BASE_URL; ?>barista/dashboard" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Volver al Dashboard
                    </a>
                </div>
            </div>

            <!-- Modal para agregar producto -->
            <div class="modal fade" id="agregarProductoModal" tabindex="-1" aria-labelledby="agregarProductoModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="agregarProductoModalLabel">Agregar Nuevo Producto</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="<?php echo BASE_URL; ?>barista/agregarProducto" method="POST">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre del Producto</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                </div>
                                <div class="mb-3">
                                    <label for="tipo" class="form-label">Tipo</label>
                                    <select class="form-control" id="tipo" name="tipo" required>
                                        <option value="ingrediente">Ingrediente</option>
                                        <option value="producto">Producto Final</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="stock" class="form-label">Stock Inicial</label>
                                    <input type="number" step="0.01" class="form-control" id="stock" name="stock" required>
                                </div>
                                <div class="mb-3">
                                    <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                                    <input type="number" step="0.01" class="form-control" id="stock_minimo" name="stock_minimo" required>
                                </div>
                                <div class="mb-3">
                                    <label for="unidad_medida" class="form-label">Unidad de Medida</label>
                                    <select class="form-control" id="unidad_medida" name="unidad_medida" required>
                                        <option value="unidades">Unidades</option>
                                        <option value="gramos">Gramos</option>
                                        <option value="ml">Mililitros</option>
                                        <option value="kg">Kilogramos</option>
                                        <option value="litros">Litros</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="precio" class="form-label">Precio (opcional)</label>
                                    <input type="number" step="0.01" class="form-control" id="precio" name="precio">
                                </div>
                                <div class="mb-3">
                                    <label for="categoria" class="form-label">Categoría (opcional)</label>
                                    <select class="form-control" id="categoria" name="categoria">
                                        <option value="">Seleccionar categoría</option>
                                        <?php if (!empty($categorias)): ?>
                                            <?php foreach ($categorias as $categoria): ?>
                                                <option value="<?php echo htmlspecialchars($categoria['nombre']); ?>">
                                                    <?php echo htmlspecialchars($categoria['nombre']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción (opcional)</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar Producto</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <?php if (!empty($ingredientes)): ?>
                        <div class="row">
                            <!-- Columna de Bebidas -->
                            <div class="col-md-6 border-end">
                                <h4 class="mb-3 text-primary">Bebidas</h4>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>Producto</th>
                                                <th>Stock Actual</th>
                                                <th>Stock Mínimo</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($ingredientes as $ingrediente): 
                                                if (strpos(strtolower($ingrediente['categoria']), 'bebida') !== false): ?>
                                                <tr class="<?php echo $ingrediente['stock'] <= $ingrediente['stock_minimo'] ? 'table-danger' : ''; ?>">
                                                    <td><?php echo htmlspecialchars($ingrediente['nombre']); ?></td>
                                                    <td><?php echo $ingrediente['stock']; ?></td>
                                                    <td><?php echo $ingrediente['stock_minimo']; ?></td>
                                                    <td>
                                                        <?php if ($ingrediente['stock'] <= $ingrediente['stock_minimo']): ?>
                                                            <span class="badge bg-danger">Faltante</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-success">OK</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-warning marcar-faltante" 
                                                                data-id="<?php echo $ingrediente['id']; ?>"
                                                                data-nombre="<?php echo htmlspecialchars($ingrediente['nombre']); ?>">
                                                            <i class="fas fa-exclamation-triangle"></i> Marcar Faltante
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endif; endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Columna de Alimentos -->
                            <div class="col-md-6">
                                <h4 class="mb-3 text-success">Alimentos</h4>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-success">
                                            <tr>
                                                <th>Producto</th>
                                                <th>Stock Actual</th>
                                                <th>Stock Mínimo</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($ingredientes as $ingrediente): 
                                                if (strpos(strtolower($ingrediente['categoria']), 'bebida') === false): ?>
                                                <tr class="<?php echo $ingrediente['stock'] <= $ingrediente['stock_minimo'] ? 'table-danger' : ''; ?>">
                                                    <td><?php echo htmlspecialchars($ingrediente['nombre']); ?></td>
                                                    <td><?php echo $ingrediente['stock']; ?></td>
                                                    <td><?php echo $ingrediente['stock_minimo']; ?></td>
                                                    <td>
                                                        <?php if ($ingrediente['stock'] <= $ingrediente['stock_minimo']): ?>
                                                            <span class="badge bg-danger">Faltante</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-success">OK</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-warning marcar-faltante" 
                                                                data-id="<?php echo $ingrediente['id']; ?>"
                                                                data-nombre="<?php echo htmlspecialchars($ingrediente['nombre']); ?>">
                                                            <i class="fas fa-exclamation-triangle"></i> Marcar Faltante
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endif; endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            No hay ingredientes registrados en el sistema.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const botonesMarcar = document.querySelectorAll('.marcar-faltante');
    
    botonesMarcar.forEach(boton => {
        boton.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            
            if (confirm(`¿Estás seguro de marcar ${nombre} como faltante?`)) {
                window.location.href = `<?php echo BASE_URL; ?>barista/marcarFaltante/${id}`;
            }
        });
    });
});
</script>
