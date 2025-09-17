<div class="card">
    <div class="card-header">Recuperar Contraseña</div>
    <div class="card-body">
        <form method="POST" action="<?php echo BASE_URL; ?>auth/forgotPassword">
            <div class="mb-3">
                <label for="email" class="form-label">Ingresa tu correo registrado</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Enviar enlace</button>
        </form>
    </div>
</div>
