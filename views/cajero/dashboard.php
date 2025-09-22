<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../models/PedidoModel.php';
require_once __DIR__ . '/../../models/PagoModel.php';
$pedidoModel = new PedidoModel();
$pagoModel = new PagoModel();

// KPIs
$pedidos_pendientes = $pedidoModel->obtenerPedidosParaPago();
$pedidos_pagados = $pedidoModel->obtenerPedidosPorEstado('pagado');
$ventas_dia = $pagoModel->obtenerTotalHoy();
$clientes_atendidos = is_array($pedidos_pagados) ? count($pedidos_pagados) : 0;

// Reporte de ventas (últimos 7 días)
$fecha_fin = date('Y-m-d');
$fecha_inicio = date('Y-m-d', strtotime('-6 days'));
$reporte = $pedidoModel->obtenerReporteVentas($fecha_inicio, $fecha_fin);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Cajero - CAFETERIA-NOVA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e342e;
            --secondary-color: #d7ccc8;
            --accent-color: #ff5722;
            --light-color: #f5f5f5;
            --dark-color: #3e2723;
            --matcha-green: #a5d6a7;
            --fruit-red: #ef9a9a;
            --ade-blue: #90caf9;
            --smoothie-yellow: #fff59d;
            --lila-color: #b39ddb;
            --rosa-color: #f8bbd9;
            --lila-suave: #e1bee7;
            --rosa-suave: #fce4ec;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            background-color: var(--primary-color);
            color: white;
            height: 100vh;
            position: fixed;
            padding-top: 20px;
            border-top-right-radius: 25px;
            border-bottom-right-radius: 25px;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            border-left: 5px solid var(--lila-color);
        }
        
        .sidebar .logo {
            text-align: center;
            padding: 0 20px 20px;
            border-bottom: 2px solid var(--lila-color);
            margin-bottom: 25px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-color) 100%);
            border-radius: 15px;
            margin: 10px;
            padding: 15px;
        }
        
        .sidebar .nav-link {
            color: var(--secondary-color);
            padding: 14px 20px;
            margin: 8px 10px;
            border-radius: 15px;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--lila-color) 0%, var(--rosa-color) 100%);
            color: var(--dark-color);
            border-left: 3px solid var(--rosa-color);
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(179, 157, 219, 0.3);
        }
        
        .sidebar .nav-link i {
            margin-right: 12px;
            color: var(--lila-suave);
        }
        
        .sidebar .nav-link:hover i, .sidebar .nav-link.active i {
            color: var(--dark-color);
        }
        
        .main-content {
            margin-left: 250px;
            padding: 25px;
        }
        
        .header {
            background: linear-gradient(135deg, #ffffff 0%, var(--rosa-suave) 100%);
            padding: 18px 25px;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(179, 157, 219, 0.2);
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 2px solid var(--lila-suave);
        }
        
        .card {
            border: none;
            border-radius: 20px !important;
            box-shadow: 0 4px 15px rgba(179, 157, 219, 0.15);
            margin-bottom: 25px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            border: 2px solid transparent;
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
        }
        
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(179, 157, 219, 0.25);
            border: 2px solid var(--lila-suave);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--lila-color) 100%);
            color: white;
            border-radius: 20px 20px 0 0 !important;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 3px solid var(--rosa-color);
        }
        
        .btn {
            border-radius: 30px !important;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--lila-color) 100%);
            border-color: var(--lila-color);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--lila-color) 0%, var(--rosa-color) 100%);
            border-color: var(--rosa-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(179, 157, 219, 0.4);
        }
        
        .badge-pending {
            background: linear-gradient(135deg, #ffc107 0%, #ffeb3b 100%);
            color: black;
            border-radius: 15px;
            padding: 6px 12px;
        }
        
        .badge-paid {
            background: linear-gradient(135deg, #28a745 0%, #4caf50 100%);
            border-radius: 15px;
            padding: 6px 12px;
        }
        
        .order-item {
            border-left: 5px solid var(--lila-color);
            padding: 18px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            border-radius: 15px;
            border: 2px solid #f0f0f0;
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .order-item:hover {
            background: linear-gradient(135deg, #ffffff 0%, var(--rosa-suave) 100%);
            border-left: 5px solid var(--rosa-color);
            transform: translateX(5px);
            box-shadow: 0 5px 20px rgba(179, 157, 219, 0.2);
        }
        
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(135deg, var(--rosa-color) 0%, var(--lila-color) 100%);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            border: 2px solid white;
            box-shadow: 0 2px 8px rgba(179, 157, 219, 0.3);
        }
        
        .payment-method {
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 18px;
            border: 2px solid transparent;
        }
        
        .payment-method:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(179, 157, 219, 0.3);
            border: 2px solid var(--lila-color);
        }
        
        .table {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: 2px solid var(--lila-suave);
        }

        .table-hover tbody tr:hover {
            background: linear-gradient(135deg, var(--rosa-suave) 0%, #ffffff 100%);
            transform: scale(1.01);
            transition: all 0.2s ease;
        }

        .table thead th {
            background: linear-gradient(135deg, var(--lila-color) 0%, var(--primary-color) 100%);
            color: white;
            border-bottom: 3px solid var(--rosa-color);
            padding: 15px;
            font-weight: 600;
        }
        
        .table tbody td {
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .notification-toast {
            position: fixed;
            bottom: 25px;
            right: 25px;
            z-index: 1100;
            min-width: 300px;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(179, 157, 219, 0.3);
            border: 2px solid var(--lila-suave);
        }
        
        .toast-header {
            background: linear-gradient(135deg, var(--lila-color) 0%, var(--rosa-color) 100%);
            color: white;
            border-radius: 18px 18px 0 0 !important;
            padding: 12px 15px;
            border-bottom: 2px solid var(--rosa-suave);
        }
        
        .refresh-btn {
            cursor: pointer;
            transition: transform 0.5s ease;
            background: linear-gradient(135deg, var(--lila-suave) 0%, #ffffff 100%);
            padding: 8px;
            border-radius: 50%;
            border: 2px solid var(--lila-color);
        }
        
        .refresh-btn:hover {
            transform: rotate(180deg);
            background: linear-gradient(135deg, var(--rosa-color) 0%, var(--lila-color) 100%);
            color: white;
        }
        
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }
        
        .pulse {
            animation: pulse 1.5s infinite ease-in-out;
        }
        
        @keyframes pulse {
            0% { 
                transform: scale(1);
                box-shadow: 0 4px 15px rgba(179, 157, 219, 0.15);
            }
            50% { 
                transform: scale(1.05);
                box-shadow: 0 8px 25px rgba(179, 157, 219, 0.3);
            }
            100% { 
                transform: scale(1);
                box-shadow: 0 4px 15px rgba(179, 157, 219, 0.15);
            }
        }
        
        .stats-number {
            font-size: 2.2rem;
            font-weight: bold;
            background: linear-gradient(135deg, var(--lila-color) 0%, var(--rosa-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .card-body {
            padding: 25px;
        }
        
        .text-primary {
            color: var(--lila-color) !important;
        }
        
        .text-success {
            color: var(--rosa-color) !important;
        }
        
        .text-warning {
            color: #ffa726 !important;
        }
        
        .alert {
            border-radius: 15px;
            border: 2px solid transparent;
            padding: 15px 20px;
            margin-bottom: 20px;
        }
        
        .alert-info {
            background: linear-gradient(135deg, #e3f2fd 0%, var(--lila-suave) 100%);
            border-color: var(--lila-color);
            color: var(--dark-color);
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--rosa-color) 0%, #f06292 100%);
            border-color: var(--rosa-color);
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, #f06292 0%, var(--rosa-color) 100%);
            border-color: #f06292;
            transform: translateY(-2px);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #ffb74d 0%, #ffa726 100%);
            border-color: #ffa726;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar d-md-block">
                <div class="logo">
                    <h3><i class="fas fa-coffee"></i> CAFETERIA-NOVA</h3>
                    <p class="text-center mb-0">Panel de Cajero</p>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>cajero/pagos">
                            <i class="fas fa-cash-register"></i> Procesar Pagos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>cajero/reportes">
                            <i class="fas fa-chart-bar"></i> Reportes
                        </a>
                    </li>
                    <li class="nav-item mt-4">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>auth/logout">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Mensaje de bienvenida siempre visible arriba -->
                <div class="header sticky-top" style="z-index: 1050;">
                    <h4 class="mb-0">Bienvenido, <span class="text-primary"><?php echo $_SESSION['usuario_nombre'] ?? 'CAJERO'; ?></span></h4>
                    <div class="d-flex align-items-center">
                        <div class="me-3 position-relative">
                            <i class="fas fa-bell fa-lg text-muted"></i>
                            <span class="notification-badge" id="notification-count">0</span>
                        </div>
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['usuario_nombre'] ?? 'Cajero'); ?>&background=4e342e&color=fff" class="rounded-circle" width="40" height="40" alt="Usuario">
                    </div>
                </div>

                <div class="row">
                    <!-- Resumen de Estado (KPIs) -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card text-center pulse" id="card-pendientes">
                            <div class="card-body">
                                <i class="fas fa-coffee fa-2x text-primary mb-2"></i>
                                <h5 class="card-title">Pedidos Pendientes</h5>
                                <h3 class="stats-number" id="pendientes-count"><?php echo is_array($pedidos_pendientes) ? count($pedidos_pendientes) : 0; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <h5 class="card-title">Pagados Hoy</h5>
                                <h3 class="stats-number text-success" id="pagados-count"><?php echo $clientes_atendidos; ?></h3>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-users fa-2x text-warning mb-2"></i>
                                <h5 class="card-title">Clientes Atendidos</h5>
                                <h3 class="stats-number text-warning" id="clientes-count"><?php echo $clientes_atendidos; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Pedidos Pendientes de Pago</h5>
                                <div>
                                    <span class="badge bg-light text-dark" id="nuevos-count"><?php echo is_array($pedidos_pendientes) ? count($pedidos_pendientes) : 0; ?> nuevos</span>
                                    <span class="refresh-btn ms-2" id="refresh-pedidos" title="Actualizar pedidos">
                                        <i class="fas fa-sync-alt"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body" id="pedidos-container">
                                <?php if (!empty($pedidos_pendientes)): ?>
                                    <?php foreach ($pedidos_pendientes as $pedido): ?>
                                        <div class="order-item mb-4">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6>Orden #<?php echo htmlspecialchars($pedido['codigo']); ?> - <?php echo htmlspecialchars($pedido['tipo']); ?></h6>
                                                <?php if ($pedido['estado'] === 'pagado'): ?>
                                                    <span class="badge bg-success">Pagado</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark">No Pagado</span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="mb-1">Cliente: <?php echo htmlspecialchars($pedido['cliente_nombre'] ?? ''); ?></p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">Creado: <?php echo date('d/m/Y H:i', strtotime($pedido['creado_en'])); ?></small>
                                                <span class="fw-bold">$<?php echo number_format($pedido['total'], 2); ?></span>
                                            </div>
                                            <div class="text-end mt-2 d-flex gap-2 justify-content-end">
                                                <a href="<?php echo BASE_URL . 'cajero/pagos?pedido_id=' . $pedido['id']; ?>" class="btn btn-sm btn-primary">Procesar Pago</a>
                                                <form method="post" action="<?php echo BASE_URL . 'cajero/togglePago'; ?>" style="display:inline;">
                                                    <input type="hidden" name="pedido_id" value="<?php echo $pedido['id']; ?>">
                                                    <input type="hidden" name="estado_actual" value="<?php echo $pedido['estado']; ?>">
                                                    <button type="submit" class="btn btn-sm <?php echo ($pedido['estado'] === 'pagado') ? 'btn-warning' : 'btn-success'; ?>">
                                                        <?php echo ($pedido['estado'] === 'pagado') ? 'Marcar No Pagado' : 'Marcar Pagado'; ?>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="alert alert-info mb-0">No hay pedidos pendientes de pago.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reporte de Ventas (últimos 7 días) -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Reporte de Ventas (Últimos 7 días)</h5>
                        <button class="btn btn-sm btn-outline-light" id="refresh-report">
                            <i class="fas fa-sync-alt"></i> Actualizar
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>N° Pedidos</th>
                                        <th>Total Ventas</th>
                                        <th>Promedio Pedido</th>
                                    </tr>
                                </thead>
                                <tbody id="reporte-body">
                                    <?php foreach ($reporte as $fila): ?>
                                    <tr>
                                        <td><?php echo $fila['fecha']; ?></td>
                                        <td><?php echo $fila['numero_pedidos']; ?></td>
                                        <td>$<?php echo number_format($fila['total_ventas'], 2); ?></td>
                                        <td>$<?php echo number_format($fila['promedio_pedido'], 2); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast para notificaciones -->
    <div class="toast notification-toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
        <div class="toast-header">
            <strong class="me-auto">Nuevos pedidos</strong>
            <small>Ahora</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Tienes <span id="toast-count">0</span> nuevos pedidos pendientes de pago
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para actualizar notificaciones
        function actualizarNotificaciones() {
            fetch('<?php echo BASE_URL; ?>notificaciones_ajax.php')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notification-count');
                    if (badge) {
                        const prevCount = parseInt(badge.textContent);
                        badge.textContent = data.nuevas;
                        
                        // Mostrar toast si hay nuevas notificaciones
                        if (data.nuevas > 0 && data.nuevas > prevCount) {
                            document.getElementById('toast-count').textContent = data.nuevas;
                            const toast = new bootstrap.Toast(document.querySelector('.notification-toast'));
                            toast.show();
                            
                            // Animación de parpadeo en el card de pendientes
                            const cardPendientes = document.getElementById('card-pendientes');
                            cardPendientes.classList.add('pulse');
                            setTimeout(() => {
                                cardPendientes.classList.remove('pulse');
                            }, 3000);
                        }
                    }
                    
                    // Actualizar contadores
                    if (data.pendientes !== undefined) {
                        document.getElementById('pendientes-count').textContent = data.pendientes;
                        document.getElementById('nuevos-count').textContent = data.pendientes + ' nuevos';
                    }
                    
                    if (data.pagados !== undefined) {
                        document.getElementById('pagados-count').textContent = data.pagados;
                        document.getElementById('clientes-count').textContent = data.pagados;
                    }
                    
                    if (data.ventas !== undefined) {
                        document.getElementById('ventas-count').textContent = '$' + parseFloat(data.ventas).toFixed(2);
                    }
                })
                .catch(error => {
                    console.error('Error al obtener notificaciones:', error);
                });
        }

        // Función para cargar pedidos pendientes
function cargarPedidosPendientes() {
    const container = document.getElementById('pedidos-container');
    container.classList.add('loading');
    
    fetch('<?php echo BASE_URL; ?>pedidos_pendientes_ajax.php')
        .then(response => {
            // Verificar si la respuesta es JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('La respuesta no es JSON');
            }
            return response.json();
        })
        .then(data => {
            container.classList.remove('loading');
            
            // Verificar si hay error en la respuesta
            if (data.error) {
                container.innerHTML = '<div class="alert alert-danger mb-0">Error: ' + data.error + '</div>';
                return;
            }
            
            if (data.length === 0) {
                container.innerHTML = '<div class="alert alert-info mb-0">No hay pedidos pendientes de pago.</div>';
                return;
            }
            
            let html = '';
            data.forEach(pedido => {
                html += `
                <div class="order-item mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6>Orden #${pedido.codigo} - ${pedido.tipo}</h6>
                        ${pedido.estado === 'pagado' ? 
                            '<span class="badge bg-success">Pagado</span>' : 
                            '<span class="badge bg-warning text-dark">No Pagado</span>'}
                    </div>
                    <p class="mb-1">Cliente: ${pedido.cliente_nombre || ''}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">Creado: ${new Date(pedido.creado_en).toLocaleString()}</small>
                        <span class="fw-bold">$${parseFloat(pedido.total).toFixed(2)}</span>
                    </div>
                    <div class="text-end mt-2 d-flex gap-2 justify-content-end">
                        <a href="<?php echo BASE_URL; ?>cajero/pagos?pedido_id=${pedido.id}" class="btn btn-sm btn-primary">Procesar Pago</a>
                        <form method="post" action="<?php echo BASE_URL; ?>cajero/togglePago" style="display:inline;">
                            <input type="hidden" name="pedido_id" value="${pedido.id}">
                            <input type="hidden" name="estado_actual" value="${pedido.estado}">
                            <button type="submit" class="btn btn-sm ${pedido.estado === 'pagado' ? 'btn-warning' : 'btn-success'}">
                                ${pedido.estado === 'pagado' ? 'Marcar No Pagado' : 'Marcar Pagado'}
                            </button>
                        </form>
                    </div>
                </div>`;
            });
            
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error al cargar pedidos:', error);
            container.classList.remove('loading');
            container.innerHTML = '<div class="alert alert-danger mb-0">Error al cargar los pedidos. Por favor, recarga la página.</div>';
        });
}

// Función temporal para debuggear
function debugFetch() {
fetch('<?php echo BASE_URL; ?>pedidos_pendientes_ajax.php')
    .then(response => response.text())
        .then(text => {
            console.log('Respuesta del servidor:', text);
        })
        .catch(error => {
            console.error('Error en debug:', error);
        });
}

// Llama a esta función para ver qué está devolviendo realmente el servidor
debugFetch();
        // Función para cargar reporte de ventas
// Función para cargar reporte de ventas
function cargarReporteVentas() {
    const btnRefresh = document.getElementById('refresh-report');
    const tbody = document.getElementById('reporte-body');
    
    btnRefresh.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    tbody.innerHTML = '<tr><td colspan="4" class="text-center">Cargando...</td></tr>';
    
    // Usar las mismas fechas que se muestran inicialmente
    const fecha_inicio = '<?php echo $fecha_inicio; ?>';
    const fecha_fin = '<?php echo $fecha_fin; ?>';
    
    fetch(`<?php echo BASE_URL; ?>cajero/reporteVentasAjax?inicio=${fecha_inicio}&fin=${fecha_fin}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            btnRefresh.innerHTML = '<i class="fas fa-sync-alt"></i> Actualizar';
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">No hay datos de ventas</td></tr>';
                return;
            }
            
            let html = '';
            data.forEach(fila => {
                html += `
                <tr>
                    <td>${fila.fecha}</td>
                    <td>${fila.numero_pedidos}</td>
                    <td>$${parseFloat(fila.total_ventas).toFixed(2)}</td>
                    <td>$${parseFloat(fila.promedio_pedido).toFixed(2)}</td>
                </tr>`;
            });
            
            tbody.innerHTML = html;
        })
        .catch(error => {
            console.error('Error al cargar reporte:', error);
            btnRefresh.innerHTML = '<i class="fas fa-sync-alt"></i> Actualizar';
            tbody.innerHTML = '<tr><td colspan="4" class="text-center">Error al cargar el reporte</td></tr>';
        });
}

        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            // Cargar datos iniciales
            actualizarNotificaciones();
            
            // Configurar intervalos de actualización
            setInterval(actualizarNotificaciones, 5000);
            
            // Configurar botones de actualización
            document.getElementById('refresh-pedidos').addEventListener('click', cargarPedidosPendientes);
            document.getElementById('refresh-report').addEventListener('click', cargarReporteVentas);
            
            // Efecto de carga inicial
            document.getElementById('card-pendientes').classList.add('pulse');
            setTimeout(() => {
                document.getElementById('card-pendientes').classList.remove('pulse');
            }, 2000);
        });
    </script>
</body>
</html>