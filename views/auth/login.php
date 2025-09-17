<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cafetería Nova</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #ff9966 0%, #ff5e62 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            max-width: 420px; /* ancho máximo fijo */
            width: 100%;      /* ocupa el ancho disponible */
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            background: #fff;
        }
        .card-header {
            background: linear-gradient(90deg, #ff5e62 0%, #ff9966 100%);
            color: white;
            text-align: center;
            padding: 25px;
        }
        .card-header h3 {
            font-weight: bold;
            letter-spacing: 1px;
        }
        .btn-primary {
            background: linear-gradient(90deg, #ff5e62 0%, #ff9966 100%);
            border: none;
            border-radius: 50px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            transform: scale(1.03);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
        }
        .form-control {
            border-radius: 12px;
            padding: 12px 20px;
            border: 2px solid #e6e6e6;
            transition: all 0.3s;
            font-size: 15px;
        }
        .form-control:focus {
            border-color: #ff5e62;
            box-shadow: 0 0 0 0.25rem rgba(255, 94, 98, 0.25);
        }
        .login-link {
            color: #ff5e62;
            font-weight: 600;
            text-decoration: none;
            transition: 0.3s;
        }
        .login-link:hover {
            text-decoration: underline;
            color: #e94e54;
        }
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 15px;
            top: 42px;
            color: #6c757d;
        }
        .form-group {
            position: relative;
        }
        hr {
            border-top: 1px solid #ddd;
            margin: 25px 0;
        }
        .alert {
            border-radius: 12px;
        }
        .text-center h6 {
            font-weight: bold;
            color: #444;
        }
        .footer {
            color: #fff;
            font-size: 14px;
            margin-top: 25px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="card-header">
            <h3 class="mb-0"><i class="fas fa-mug-hot me-2"></i>Cafetería Nova</h3>
            <p class="mb-0 small">Bienvenido, inicia sesión</p>
        </div>
        <div class="card-body p-4">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo BASE_URL; ?>auth/login">
                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="ejemplo@cafenova.com" required>
                </div>
                <div class="mb-3 form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="********" required>
                    <span class="password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                </button>
            </form>
            
            <hr>
            
            <div class="text-center">
                <h6>Credenciales de prueba:</h6>
                <p><strong>Admin:</strong> admin1@cafenova.com / password</p>
                <p><strong>Mesero:</strong> mesero@cafenova.com / password</p>
            </div>
            
            <div class="text-center mt-4">
                <p>¿No tienes cuenta? <a href="<?php echo BASE_URL; ?>auth/registrar" class="login-link">Regístrate aquí</a></p>
            </div>
            <div class="text-center mt-3">
                <a href="<?php echo BASE_URL; ?>auth/forgotPassword" class="login-link">
                    ¿Olvidaste tu contraseña?
                </a>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>© 2023 Cafetería Nova. Todos los derechos reservados.</p>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.parentNode.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
