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
            border-radius: 5px;
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
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(138, 39, 214, 0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(140, 23, 236, 0.1);
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 8px 8px 0 0 !important;
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
            padding-left: 15px;
            margin-bottom: 15px;
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
        }
        
        .payment-method:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(78, 52, 46, 0.05);
        }
        
        .menu-badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
        
        .badge-cafe {
            background-color: var(--primary-color);
        }
        
        .badge-te {
            background-color: var(--matcha-green);
            color: black;
        }
        
        .badge-ade {
            background-color: var(--ade-blue);
            color: black;
        }
        
        .badge-smoothie {
            background-color: var(--smoothie-yellow);
            color: black;
        }
        
        .category-title {
            border-left: 4px solid;
            padding-left: 10px;
            margin: 20px 0 10px;
        }
        
        .category-cafe {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .category-te {
            border-color: var(--matcha-green);
            color: var(--matcha-green);
        }
        
        .category-ade {
            border-color: var(--ade-blue);
            color: var(--ade-blue);
        }
        
        .category-smoothie {
            border-color: var(--smoothie-yellow);
            color: #f57f17;
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
                        <a class="nav-link" href="#">
                            <i class="fas fa-cash-register"></i> Procesar Pagos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
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
                    <h4 class="mb-0">Bienvenido, <span class="text-primary">CAJERO</span></h4>
                    <div class="d-flex align-items-center">
                        <div class="me-3 position-relative">
                            <i class="fas fa-bell fa-lg text-muted"></i>
                            <span class="notification-badge">5</span>
                        </div>
                        <img src="https://ui-avatars.com/api/?name=Cajero&background=4e342e&color=fff" class="rounded-circle" width="40" height="40" alt="Usuario">
                    </div>
                </div>

                <div class="row">
                    <!-- Resumen de Estado (KPIs) -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-coffee fa-2x text-primary mb-2"></i>
                                <h5 class="card-title">Pedidos Pendientes</h5>
                                <h3 class="text-primary"><?php echo is_array($pedidos_pendientes) ? count($pedidos_pendientes) : 0; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <h5 class="card-title">Pagados Hoy</h5>
                                <h3 class="text-success"><?php echo $clientes_atendidos; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-money-bill-wave fa-2x text-info mb-2"></i>
                                <h5 class="card-title">Ventas del Día</h5>
                                <h3 class="text-info">$<?php echo number_format($ventas_dia, 2); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-users fa-2x text-warning mb-2"></i>
                                <h5 class="card-title">Clientes Atendidos</h5>
                                <h3 class="text-warning"><?php echo $clientes_atendidos; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Pedidos Pendientes de Pago</h5>
                                <span class="badge bg-light text-dark"><?php echo is_array($pedidos_pendientes) ? count($pedidos_pendientes) : 0; ?> nuevos</span>
                            </div>
                            <div class="card-body">
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
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Reporte de Ventas (Últimos 7 días)</h5>
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
                                <tbody>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Funcionalidad básica para seleccionar método de pago
        document.querySelectorAll('.payment-method').forEach(method => {
            method.addEventListener('click', function() {
                document.querySelectorAll('.payment-method').forEach(m => {
                    m.style.border = '1px solid #dee2e6';
                });
                this.style.border = '2px solid #4e342e';
                
                // Si es tarjeta, ocultar campo de cambio
                const changeInput = document.querySelector('input[readonly]');
                const amountInput = document.querySelector('input[type="number"]');
                if (this.textContent.includes('Tarjeta')) {
                    changeInput.value = '$0.00';
                    amountInput.value = '24.20';
                    amountInput.readOnly = true;
                } else {
                    amountInput.readOnly = false;
                    amountInput.value = '';
                    changeInput.value = '$0.00';
                }
            });
        });

        // Calcular cambio cuando se ingresa monto en efectivo
        document.querySelector('input[type="number"]').addEventListener('input', function() {
            const total = 24.20;
            const received = parseFloat(this.value) || 0;
            const change = received - total;
            
            const changeInput = document.querySelector('input[readonly]');
            if (change >= 0) {
                changeInput.value = '$' + change.toFixed(2);
            } else {
                changeInput.value = 'Fondos insuficientes';
            }
        });

        // Simulación de notificaciones
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const badge = this.querySelector('.notification-badge');
                if (badge) {
                    badge.style.display = 'none';
                }
            });
        });
        

    </script>
</body>
</html>
