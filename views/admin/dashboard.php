<div class="row">
    <div class="col-md-12">
        <h1>Panel de Administración</h1>
        <p>Bienvenido, <?php echo $_SESSION['usuario_nombre']; ?></p>
        
        <div class="row mt-4">
            <!-- Usuarios -->
            <div class="col-md-3">
                <a href="<?php echo BASE_URL; ?>admin/usuarios" class="text-decoration-none">
                    <div class="card text-white bg-primary mb-3 hover-card">
                        <div class="card-body">
                            <h5 class="card-title">Usuarios</h5>
                            <p class="card-text">Gestionar empleados</p>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Productos -->
            <div class="col-md-3">
                <a href="<?php echo BASE_URL; ?>admin/productos" class="text-decoration-none">
                    <div class="card text-white bg-success mb-3 hover-card">
                        <div class="card-body">
                            <h5 class="card-title">Productos</h5>
                            <p class="card-text">Gestionar menú</p>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Reportes -->
            <div class="col-md-3">
                <a href="<?php echo BASE_URL; ?>admin/reportes" class="text-decoration-none">
                    <div class="card text-white bg-warning mb-3 hover-card">
                        <div class="card-body">
                            <h5 class="card-title">Reportes</h5>
                            <p class="card-text">Ver reportes</p>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Inventario -->
            <div class="col-md-3">
                <a href="<?php echo BASE_URL; ?>admin/inventario" class="text-decoration-none">
                    <div class="card text-white bg-info mb-3 hover-card">
                        <div class="card-body">
                            <h5 class="card-title">Inventario</h5>
                            <p class="card-text">Control de stock</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>