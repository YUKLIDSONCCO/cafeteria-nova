<div class="row">
    <div class="col-md-12 text-center mb-4">
        <h1 style="color:#6a0dad; text-shadow: 1px 1px 2px #ff99cc;">✨ PANEL DE ADMINISTRACION✨</h1>
        <p class="text-muted">Bienvenido, <strong><?php echo $_SESSION['usuario_nombre']; ?></strong></p>
    </div>
</div>

<div class="row mt-4">
    <!-- Usuarios -->
    <div class="col-md-3 mb-4">
        <a href="<?php echo BASE_URL; ?>admin/usuarios" class="text-decoration-none">
            <div class="card hover-card shadow-lg rounded-4" style="background: linear-gradient(135deg, #d6a4f4, #f6c1f6); border: none; transition: transform 0.3s;">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x mb-3" style="color:#4a148c;"></i>
                    <h5 class="card-title fw-bold" style="color:#4a148c;">Usuarios</h5>
                    <p class="card-text text-dark">Gestionar empleados</p>
                </div>
            </div>
        </a>
    </div>
    
    <!-- Productos -->
    <div class="col-md-3 mb-4">
        <a href="<?php echo BASE_URL; ?>admin/productos" class="text-decoration-none">
            <div class="card hover-card shadow-lg rounded-4" style="background: linear-gradient(135deg, #f4a1f5, #f9c2f7); border: none; transition: transform 0.3s;">
                <div class="card-body text-center">
                    <i class="fas fa-coffee fa-3x mb-3" style="color:#6a0dad;"></i>
                    <h5 class="card-title fw-bold" style="color:#6a0dad;">Productos</h5>
                    <p class="card-text text-dark">Gestionar menú</p>
                </div>
            </div>
        </a>
    </div>
    
    <!-- Reportes -->
    <div class="col-md-3 mb-4">
        <a href="<?php echo BASE_URL; ?>admin/reportes" class="text-decoration-none">
            <div class="card hover-card shadow-lg rounded-4" style="background: linear-gradient(135deg, #dca0f4, #f6b4f7); border: none; transition: transform 0.3s;">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-3x mb-3" style="color:#4a148c;"></i>
                    <h5 class="card-title fw-bold" style="color:#4a148c;">Reportes</h5>
                    <p class="card-text text-dark">Ver reportes</p>
                </div>
            </div>
        </a>
    </div>
    
    <!-- Inventario -->
    <div class="col-md-3 mb-4">
        <a href="<?php echo BASE_URL; ?>admin/inventario" class="text-decoration-none">
            <div class="card hover-card shadow-lg rounded-4" style="background: linear-gradient(135deg, #e0aefc, #f6d0fa); border: none; transition: transform 0.3s;">
                <div class="card-body text-center">
                    <i class="fas fa-boxes fa-3x mb-3" style="color:#6a0dad;"></i>
                    <h5 class="card-title fw-bold" style="color:#6a0dad;">Inventario</h5>
                    <p class="card-text text-dark">Control de stock</p>
                </div>
            </div>
        </a>
    </div>
</div>

<style>
.hover-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 20px rgba(0,0,0,0.25);
}
</style>

<!-- Asegúrate de incluir Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
