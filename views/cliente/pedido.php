<div class="row">
    <div class="col-md-8">
        <h2>Realizar Pedido</h2>
        
        <div class="row" id="productos-container">
            <?php foreach ($productos as $producto): ?>
            <div class="col-md-4 mb-3">
                <div class="card producto-card" data-id="<?php echo $producto['id']; ?>" 
                     data-nombre="<?php echo htmlspecialchars($producto['nombre']); ?>" 
                     data-precio="<?php echo $producto['precio']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                        <p class="card-text">$<?php echo number_format($producto['precio'], 2); ?></p>
                        <button class="btn btn-sm btn-outline-primary agregar-producto">Agregar</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>Mi Pedido</h4>
            </div>
            <div class="card-body">
                <form id="form-pedido" action="<?php echo BASE_URL; ?>cliente/pedido" method="POST">
                    <div id="items-pedido">
                        <p class="text-muted">No hay productos agregados</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tipo de Pedido</label>
                        <select class="form-select" name="tipo" required>
                            <option value="mesa">Para la mesa</option>
                            <option value="llevar">Para llevar</option>
                        </select>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">Confirmar Pedido</button>
                    </div>
                    
                    <input type="hidden" name="productos" id="productos-data">
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productos = [];
    const itemsContainer = document.getElementById('items-pedido');
    const productosData = document.getElementById('productos-data');
    
    // Agregar producto al pedido
    document.querySelectorAll('.agregar-producto').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.producto-card');
            const id = card.dataset.id;
            const nombre = card.dataset.nombre;
            const precio = parseFloat(card.dataset.precio);
            
            // Buscar si ya existe el producto
            const index = productos.findIndex(p => p.id == id);
            
            if (index === -1) {
                productos.push({ id, nombre, precio, cantidad: 1 });
            } else {
                productos[index].cantidad++;
            }
            
            actualizarVista();
        });
    });
    
    function actualizarVista() {
        if (productos.length === 0) {
            itemsContainer.innerHTML = '<p class="text-muted">No hay productos agregados</p>';
            return;
        }
        
        let html = '';
        let total = 0;
        
        productos.forEach((producto, index) => {
            const subtotal = producto.precio * producto.cantidad;
            total += subtotal;
            
            html += `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <span class="fw-bold">${producto.nombre}</span>
                        <br>
                        <small>$${producto.precio.toFixed(2)} x ${producto.cantidad}</small>
                    </div>
                    <div>
                        <span class="fw-bold">$${subtotal.toFixed(2)}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger ms-2" 
                                onclick="eliminarProducto(${index})">×</button>
                    </div>
                </div>
            `;
        });
        
        html += `<hr><div class="d-flex justify-content-between fw-bold">
            <span>Total:</span>
            <span>$${total.toFixed(2)}</span>
        </div>`;
        
        itemsContainer.innerHTML = html;
        productosData.value = JSON.stringify(productos);
    }
    
    window.eliminarProducto = function(index) {
        productos.splice(index, 1);
        actualizarVista();
    };
});
</script>
<style>
.producto-card {
    cursor: pointer;
}
</style>
<style>
/* 🔹 Hacer más grande y llamativo el bloque "Mi Pedido" */
.col-md-4 .card {
    transform: scale(1.1);      /* Agrandar la tarjeta */
    font-size: 1.1rem;          /* Texto más grande */
}

.col-md-4 .card-header h4 {
    font-size: 1.6rem;          /* Título más grande */
    font-weight: bold;
}

.col-md-4 .btn-success {
    font-size: 1.2rem;          /* Botón Confirmar Pedido más grande */
    padding: 12px;
    border-radius: 10px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productos = [];
    const itemsContainer = document.getElementById('items-pedido');
    const productosData = document.getElementById('productos-data');
    
    // 🔹 Hacer clickeable toda la tarjeta
    document.querySelectorAll('.producto-card').forEach(card => {
        card.addEventListener('click', function() {
            const id = this.dataset.id;
            const nombre = this.dataset.nombre;
            const precio = parseFloat(this.dataset.precio);
            
            // Buscar si ya existe el producto
            const index = productos.findIndex(p => p.id == id);
            
            if (index === -1) {
                productos.push({ id, nombre, precio, cantidad: 1 });
            } else {
                productos[index].cantidad++;
            }
            
            actualizarVista();
        });
    });

    function actualizarVista() {
        if (productos.length === 0) {
            itemsContainer.innerHTML = '<p class="text-muted">No hay productos agregados</p>';
            return;
        }
        
        let html = '';
        let total = 0;
        
        productos.forEach((producto, index) => {
            const subtotal = producto.precio * producto.cantidad;
            total += subtotal;
            
            html += `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <span class="fw-bold">${producto.nombre}</span>
                        <br>
                        <small>$${producto.precio.toFixed(2)} x ${producto.cantidad}</small>
                    </div>
                    <div>
                        <span class="fw-bold">$${subtotal.toFixed(2)}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger ms-2" 
                                onclick="eliminarProducto(${index})">×</button>
                    </div>
                </div>
            `;
        });
        
        html += `<hr><div class="d-flex justify-content-between fw-bold">
            <span>Total:</span>
            <span>$${total.toFixed(2)}</span>
        </div>`;
        
        itemsContainer.innerHTML = html;
        productosData.value = JSON.stringify(productos);
    }
    
    window.eliminarProducto = function(index) {
        productos.splice(index, 1);
        actualizarVista();
    };
});
</script>
