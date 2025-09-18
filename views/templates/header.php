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
  <style>
    body {
    background: linear-gradient(135deg, rgba(251,194,235,0.7) 0%, rgba(166,193,238,0.7) 100%),
                url("https://i.pinimg.com/originals/07/5a/de/075ade028580e551fc227649650c79d2.gif");
    background-size: cover;       /* La imagen cubre toda la pantalla */
    background-position: center;  /* Centrada */
    background-repeat: no-repeat; /* No se repite */
    background-attachment: fixed; /* Fijo en scroll */
    
    font-family: 'Poppins', sans-serif;
    color: #222;                  /* Color de texto base */
    min-height: 100vh;
    display: flex;
    flex-direction: column;       /* Mantén el flujo de página */
    }

    /* Navbar con tonos sutiles rosa, lila y azul */
    .navbar {
      background: linear-gradient(90deg, rgba(255,182,193,0.9), rgba(186,85,211,0.85), rgba(135,206,250,0.9));
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
      border-radius: 20px;
      margin: 1rem auto;
      max-width: 1200px;
      box-shadow: 4px 4px 16px rgba(0,0,0,0.1),
                  -4px -4px 16px rgba(255,255,255,0.6);
      padding: 0.9rem 1.5rem;
      transition: all 0.3s ease;
    }
    .navbar:hover {
      box-shadow: 6px 6px 20px rgba(0,0,0,0.15),
                  -6px -6px 20px rgba(255,255,255,0.8);
    }

    .navbar-brand {
      font-size: 1.6rem;
      font-weight: 700;
      background: linear-gradient(90deg, #f7f3f4ff, #fbfafdff, #fafbfdff);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      display: flex;
      align-items: center;
    }
    .navbar-brand img {
      border-radius: 50%;
      height: 42px;
      width: 42px;
      margin-right: 10px;
      box-shadow: 2px 2px 8px rgba(0,0,0,0.25);
    }

    .nav-link {
      font-weight: 500;
      color: #fff !important;
      margin: 0 0.4rem;
      transition: all 0.3s ease;
      padding: 0.5rem 0.9rem;
      border-radius: 12px;
    }
    .nav-link:hover {
      background: rgba(255,255,255,0.25);
      color: #222 !important;
      transform: translateY(-2px);
    }

    /* Dropdown */
    .dropdown-menu {
      border-radius: 14px;
      border: none;
      padding: 0.7rem;
      box-shadow: 0 8px 24px rgba(0,0,0,0.12);
      background: #fff;
    }
    .dropdown-item {
      border-radius: 10px;
      font-weight: 500;
      transition: all 0.2s ease;
    }
    .dropdown-item:hover {
      background: #f3e5f5;
      color: #6a1b9a;
    }

    /* Badge notificaciones */
    .badge {
      font-size: 0.75rem;
      padding: 0.4em 0.6em;
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <a class="navbar-brand" href="<?php echo BASE_URL; ?>">
        <img src="<?php echo BASE_URL; ?>img/logo.jpg" alt="Cafetería Nova">
        Cafetería Nova
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNova" aria-controls="navbarNova" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNova">
        <ul class="navbar-nav ms-auto align-items-center">

          <?php if ($usuarioLogueado): ?>
          <!-- Notificaciones -->
          <li class="nav-item dropdown me-3">
            <a class="nav-link position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-bell-fill fs-5"></i>
              <span id="notificaciones-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">0</span>
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
          </li>

          <!-- Usuario -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle fw-semibold" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                  <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                </a>
              </li>
            </ul>
          </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link fw-semibold text-dark" style="background: rgba(255,255,255,0.5);" href="<?php echo BASE_URL; ?>auth/login">
                <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Contenedor para toasts de notificaciones -->
  <div id="notificaciones-toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;"></div>

  <div class="container mt-4">
