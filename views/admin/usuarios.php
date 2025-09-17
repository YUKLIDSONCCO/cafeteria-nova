<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestión de Usuarios</h1>
            <a href="<?php echo BASE_URL; ?>admin/usuarios/crear" class="btn btn-success">
                <i class="bi bi-person-plus"></i> Nuevo Usuario
            </a>
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
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Fecha Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($usuarios)): ?>
                                <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?php echo $usuario['id']; ?></td>
                                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo ucfirst($usuario['rol']); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($usuario['activo']): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($usuario['creado_en'])); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <?php if (!$usuario['activo']): ?>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="usuario_id" value="<?php echo $usuario['id']; ?>">
                                                    <input type="hidden" name="action" value="activar">
                                                    <button type="submit" class="btn btn-sm btn-success" 
                                                            onclick="return confirm('¿Activar este usuario?')">
                                                        <i class="bi bi-check-circle"></i> Activar
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="usuario_id" value="<?php echo $usuario['id']; ?>">
                                                    <input type="hidden" name="action" value="desactivar">
                                                    <button type="submit" class="btn btn-sm btn-warning" 
                                                            onclick="return confirm('¿Desactivar este usuario?')">
                                                        <i class="bi bi-x-circle"></i> Desactivar
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <?php if ($usuario['rol'] != 'administrador'): ?>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="usuario_id" value="<?php echo $usuario['id']; ?>">
                                                    <input type="hidden" name="action" value="eliminar">
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('¿Eliminar permanentemente este usuario?')">
                                                        <i class="bi bi-trash"></i> Eliminar
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        No hay usuarios registrados
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