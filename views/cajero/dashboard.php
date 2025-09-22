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
            border-top-right-radius: 20px;
            border-bottom-right-radius: 20px;
        }
        
        .sidebar .logo {
            text-align: center;
            padding: 0 20px 20px;
            border-bottom: 1px solid var(--secondary-color);
            margin-bottom: 20px;
        }
        
        .sidebar .nav-link {
            color: var(--secondary-color);
            padding: 12px 20px;
            margin: 6px 0;
            border-radius: 12px;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: var(--dark-color);
            color: white;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .header {
            background-color: white;
            padding: 15px 20px;
            border-radius: 16px;
            box-shadow: 0 2px 6px rgba(138, 39, 214, 0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card {
            border: none;
            border-radius: 18px !important;
            box-shadow: 0 2px 6px rgba(140, 23, 236, 0.1);
            margin-bottom: 20px;
            transition: transform 0.3s;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 18px 18px 0 0 !important;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .btn {
            border-radius: 25px !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--dark-color);
            border-color: var(--dark-color);
        }
        
        .badge-pending {
            background-color: #ffc107;
            color: black;
        }
        
        .badge-paid {
            background-color: #28a745;
        }
        
        .order-item {
            border-left: 4px solid var(--accent-color);
            padding: 12px;
            margin-bottom: 15px;
            transition: all 0.3s;
            border-radius: 12px;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .order-item:hover {
            background-color: rgba(255, 87, 34, 0.05);
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--accent-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        
        .payment-method {
            cursor: pointer;
            transition: all 0.3s;
            border-radius: 14px;
        }
        
        .payment-method:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .table {
            border-radius: 12px;
            overflow: hidden;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(78, 52, 46, 0.05);
        }

        .table thead th {
            border-bottom: 2px solid rgba(0,0,0,0.08);
        }
        
        .notification-toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1100;
            min-width: 250px;
            border-radius: 14px;
            overflow: hidden;
        }
        
        .toast-header {
            border-radius: 14px 14px 0 0 !important;
        }
        
        .refresh-btn {
            cursor: pointer;
            transition: transform 0.5s;
        }
        
        .refresh-btn:hover {
            transform: rotate(180deg);
        }
        
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }
        
        .pulse {
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
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
                <div class="header sticky-top bg-white" style="z-index: 1050; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
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
                                <h3 class="stats-number text-primary" id="pendientes-count"><?php echo is_array($pedidos_pendientes) ? count($pedidos_pendientes) : 0; ?></h3>
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
                                <i class="fas fa-money-bill-wave fa-2x text-info mb-2"></i>
                                <h5 class="card-title">Ventas del Día</h5>
                                <h3 class="stats-number text-info" id="ventas-count">$<?php echo number_format($ventas_dia, 2); ?></h3>
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
