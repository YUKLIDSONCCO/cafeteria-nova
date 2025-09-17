<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Cafetería Nova</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px 0;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            text-align: center;
            padding: 20px;
        }
        .btn-primary {
            background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%);
            border: none;
            border-radius: 50px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 20px;
            border: 2px solid #eee;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 0 0.25rem rgba(106, 17, 203, 0.25);
        }
        .alert-info {
            background-color: #e0f7fa;
            border-color: #4dd0e1;
            color: #006064;
            border-radius: 10px;
        }
        .login-link {
            color: #6a11cb;
            font-weight: 600;
            text-decoration: none;
        }
        .login-link:hover {
            text-decoration: underline;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0"><i class="fas fa-user-plus me-2"></i>Registro de Usuario</h3>
                    </div>
                    <div class="card-body p-4">
                        <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>
                        
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Nota:</strong> Su cuenta deberá ser activada por un administrador antes de poder acceder al sistema.
                        </div>
                        
                        <form method="POST" action="<?php echo BASE_URL; ?>auth/registrar" class="mt-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre Completo</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                                    </div>
 

                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                                <div class="mb-3">
    <label for="email_recuperacion" class="form-label">Correo de recuperación</label>
    <input type="email" class="form-control" name="email_recuperacion" required>
</div>

                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 form-group">
                                        <label for="password" class="form-label">Contraseña</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <span class="password-toggle" onclick="togglePassword('password')">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 form-group">
                                        <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        <span class="password-toggle" onclick="togglePassword('confirm_password')">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="rol" class="form-label">Rol</label>
                                        <select class="form-select" id="rol" name="rol" required>
                                            <option value="">Seleccionar rol...</option>
                                            <option value="mesero">Mesero</option>
                                            <option value="cajero">Cajero</option>
                                            <option value="barista">Barista</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="terminos" name="terminos" required>
                                <label class="form-check-label" for="terminos">
                                    Acepto los términos y condiciones
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-user-plus me-2"></i>Registrarse
                            </button>
                        </form>
                        
                        <hr class="my-4">
                        
                        <div class="text-center">
                            <p>¿Ya tienes una cuenta? <a href="<?php echo BASE_URL; ?>auth/login" class="login-link">Inicia sesión aquí</a></p>
                        </div>
                    </div>
                </div>
                
                <div class="text-center text-white mt-4">
                    <p>© 2023 Cafetería Nova. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función para mostrar/ocultar contraseña
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
        
        // Validación del formulario
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden. Por favor, inténtelo de nuevo.');
            }
        });
    </script>
</body>
</html>