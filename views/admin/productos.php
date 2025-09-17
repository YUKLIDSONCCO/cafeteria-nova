<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestión de Productos</h1>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalProducto">
                <i class="bi bi-plus-circle"></i> Nuevo Producto
            </button>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($productos)): ?>
                                <?php foreach ($productos as $producto): ?>
                                <tr>
                                    <td><?php echo $producto['id']; ?></td>
                                    <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo ucfirst($producto['categoria']); ?></span>
                                    </td>
                                    <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                                    <td>
                                        <span class="badge <?php echo $producto['stock'] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo $producto['stock']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($producto['disponible']): ?>
                                            <span class="badge bg-success">Disponible</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">No Disponible</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                    data-bs-toggle="modal" data-bs-target="#modalProducto"
                                                    onclick="cargarDatosEdicion(<?php echo htmlspecialchars(json_encode($producto)); ?>)">
                                                <i class="bi bi-pencil"></i> Editar
                                            </button>

                                            <?php if ($producto['disponible']): ?>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                                                    <input type="hidden" name="action" value="desactivar">
                                                    <button type="submit" class="btn btn-sm btn-warning" 
                                                            onclick="return confirm('¿Desactivar este producto?')">
                                                        <i class="bi bi-x-circle"></i> Desactivar
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                                                    <input type="hidden" name="action" value="activar">
                                                    <button type="submit" class="btn btn-sm btn-success" 
                                                            onclick="return confirm('¿Activar este producto?')">
                                                        <i class="bi bi-check-circle"></i> Activar
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                                                <input type="hidden" name="action" value="eliminar">
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('¿Eliminar permanentemente este producto?')">
                                                    <i class="bi bi-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        No hay productos registrados
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear/Editar Producto -->
<div class="modal fade" id="modalProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitulo">Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="productoId">
                    <input type="hidden" name="action" id="accion" value="crear">
                    
                    <div class="mb-3">
                        <label class="form-label">Nombre del Producto</label>
                        <input type="text" class="form-control" name="nombre" id="productoNombre" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select class="form-select" name="categoria" id="productoCategoria" required>
                            <option value="">Seleccionar categoría</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?php echo htmlspecialchars($categoria); ?>">
                                    <?php echo ucfirst($categoria); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Precio</label>
                        <input type="number" class="form-control" name="precio" id="productoPrecio" step="0.01" min="0" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Stock</label>
                        <input type="number" class="form-control" name="stock" id="productoStock" min="0" required>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="disponible" id="productoDisponible" value="1" checked>
                        <label class="form-check-label" for="productoDisponible">
                            Producto disponible
                        </label>
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

<script>
function cargarDatosEdicion(producto) {
    document.getElementById('modalTitulo').textContent = 'Editar Producto';
    document.getElementById('productoId').value = producto.id;
    document.getElementById('accion').value = 'editar';
    document.getElementById('productoNombre').value = producto.nombre;
    document.getElementById('productoCategoria').value = producto.categoria;
    document.getElementById('productoPrecio').value = producto.precio;
    document.getElementById('productoStock').value = producto.stock;
    document.getElementById('productoDisponible').checked = producto.disponible == 1;
}

// Resetear modal cuando se cierra
document.getElementById('modalProducto').addEventListener('hidden.bs.modal', function () {
    document.getElementById('modalTitulo').textContent = 'Nuevo Producto';
    document.getElementById('productoId').value = '';
    document.getElementById('accion').value = 'crear';
    this.querySelector('form').reset();
});
</script>