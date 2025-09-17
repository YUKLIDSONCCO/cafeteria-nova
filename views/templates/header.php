<?php
// Verificar estado de sesión de forma segura
$sessionActive = session_status() === PHP_SESSION_ACTIVE;
$usuarioLogueado = $sessionActive && isset($_SESSION['usuario_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafetería Nova - <?php echo $data['titulo'] ?? 'Inicio'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #fafafa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        /* Navbar */
        .navbar {
            background: linear-gradient(90deg, #f12711 0%, #f5af19 100%);
            padding: 0.8rem 1rem;
            border-bottom: 2px solid rgba(255,255,255,0.2);
            box-shadow: 0 6px 18px rgba(0,0,0,0.15);
        }
        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .navbar-brand img {
            filter: drop-shadow(1px 1px 2px rgba(0,0,0,0.5));
        }
        .nav-link {
            color: #fff !important;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: #ffe082 !important;
            transform: translateY(-2px);
        }
        /* Dropdown */
        .dropdown-menu {
            border-radius: 12px;
            border: none;
            padding: 0.5rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            animation: fadeIn 0.25s ease-in-out;
        }
        .dropdown-item {
            border-radius: 8px;
            transition: background 0.2s;
        }
        .dropdown-item:hover {
            background: #f8f9fa;
        }
        .dropdown-header {
            font-weight: 600;
            color: #555;
        }
        /* Notificaciones */
        .badge {
            font-size: 0.75rem;
            padding: 0.4em 0.6em;
        }
        /* Animación */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        /* Toast */
        .toast {
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand text-white d-flex align-items-center" href="<?php echo BASE_URL; ?>">
                <img src="<?php echo BASE_URL; ?>img/logo.png" alt="Cafetería Nova" height="38" class="me-2">
                Cafetería Nova
            </a>

            <?php if ($usuarioLogueado): ?>
            <div class="navbar-nav ms-auto d-flex align-items-center">
                <!-- Notificaciones -->
                <div class="nav-item dropdown me-3">
                    <a class="nav-link position-relative" href="#" id="notificationsDropdown" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell-fill fs-5"></i>
                        <span id="notificaciones-badge" 
                              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">
                            0
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                        <li><h6 class="dropdown-header"><i class="bi bi-bell me-2"></i>Notificaciones</h6></li>
                        <li><div class="dropdown-item-text small text-muted" id="notificaciones-list"></div></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-center text-primary fw-semibold" href="#" id="mark-all-notifications">
                                <i class="bi bi-check2-circle me-1"></i> Marcar todas como leídas
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Usuario -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle fw-semibold text-white" href="#" id="userDropdown" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle me-1"></i>
                        <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <span class="dropdown-item-text">
                                <i class="bi bi-award me-2 text-warning"></i>
                                Rol: <?php echo htmlspecialchars($_SESSION['usuario_rol']); ?>
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger fw-semibold" href="<?php echo BASE_URL; ?>auth/logout">
                                <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Contenedor para toasts de notificaciones -->
    <div id="notificaciones-toast-container" 
         class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;"></div>

    <div class="container mt-4">
