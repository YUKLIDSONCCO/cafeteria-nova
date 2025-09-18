<div class="row">
    <div class="col-md-8">
        <h2 class="mb-4 fw-bold text-success">Realizar Pedido</h2>
        
        <div class="row" id="productos-container">
            <?php foreach ($productos as $producto): ?>
            <div class="col-md-4 mb-4">
                <div class="card producto-card border-0 shadow-sm rounded-4 overflow-hidden h-100" 
                     data-id="<?php echo $producto['id']; ?>" 
                     data-nombre="<?php echo htmlspecialchars($producto['nombre']); ?>" 
                     data-precio="<?php echo $producto['precio']; ?>">

                    <!-- 🔹 Imagen del producto -->
                    <div class="overflow-hidden">
                        <img src="<?php echo BASE_URL; ?>img/<?php echo $producto['id']; ?>.jpg"
                             class="card-img-top producto-img"
                             alt="<?php echo $producto['nombre']; ?>"
                             style="height: 160px; object-fit: cover;">
                    </div>

                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold text-dark"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                        <p class="card-text text-success fs-5 fw-semibold">S/<?php echo number_format($producto['precio'], 2); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-lg rounded-4 border-0">
            <div class="card-header bg-success text-white rounded-top-4">
                <h4 class="mb-0 fw-bold">🛒 Mi Pedido</h4>
            </div>
            <div class="card-body">
                <form id="form-pedido" action="<?php echo BASE_URL; ?>cliente/pedido" method="POST">
                    <div id="items-pedido">
                        <p class="text-muted">No hay productos agregados</p>
                    </div>
                    
                    <div class="mb-3 mt-3">
                        <label class="form-label fw-semibold">Tipo de Pedido</label>
                        <select class="form-select shadow-sm rounded-3" name="tipo" id="tipo-pedido" required>
                            <option value="mesa">Para la mesa</option>
                            <option value="llevar">Para llevar</option>
                        </select>
                    </div>

                    <!-- 🔹 Nombre del cliente (solo si es "Para llevar") -->
                    <div class="mb-3 alert alert-warning rounded-3" id="nombre-cliente-container" style="display:none;">
                        <label class="form-label fw-bold">👤 Nombre del Cliente</label>
                        <input type="text" class="form-control shadow-sm rounded-3" name="nombre_cliente" placeholder="Escribe tu nombre">
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg shadow-sm rounded-3 fw-bold">✅ Confirmar Pedido</button>
                    </div>
                    
                    <input type="hidden" name="productos" id="productos-data">
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Estilos extra -->
<style>
/* Animación hover en tarjetas */
.producto-card {
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.producto-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
}
/* Zoom suave en imágenes */
.producto-img {
    transition: transform 0.3s ease;
}
.producto-card:hover .producto-img {
    transform: scale(1.05);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productos = [];
    const itemsContainer = document.getElementById('items-pedido');
    const productosData = document.getElementById('productos-data');

    const tipoPedido = document.getElementById('tipo-pedido');
    const nombreClienteContainer = document.getElementById('nombre-cliente-container');

    // 🔹 Mostrar/ocultar campo de nombre
    tipoPedido.addEventListener('change', function() {
        if (this.value === "llevar") {
            nombreClienteContainer.style.display = "block";
        } else {
            nombreClienteContainer.style.display = "none";
        }
    });
    
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
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded-3 shadow-sm">
                    <div>
                        <span class="fw-bold">${producto.nombre}</span>
                        <br>
                        <small class="text-muted">S/${producto.precio.toFixed(2)} x ${producto.cantidad}</small>
                    </div>
                    <div>
                        <span class="fw-bold text-success">S/${subtotal.toFixed(2)}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger ms-2 rounded-circle" 
                                onclick="eliminarProducto(${index})">×</button>
                    </div>
                </div>
            `;
        });
        
        html += `<hr><div class="d-flex justify-content-between fw-bold fs-5">
            <span>Total:</span>
            <span class="text-success">S/${total.toFixed(2)}</span>
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
