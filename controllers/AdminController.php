<?php
require_once '../core/BaseController.php';
require_once '../core/Sesion.php';

class AdminController extends BaseController {
    public function __construct() {
        // No llamar session_start() aquí
        Sesion::verificarRol('administrador');
    }
    
    public function dashboard() {
        $data = ['titulo' => 'Panel de Administración'];
        $this->view('admin/dashboard', $data);
    }
    public function usuarios() {
    $usuarioModel = $this->model('UsuarioModel');
    
    // Obtener todos los usuarios (excepto el propio administrador)
    $usuarios = $usuarioModel->obtenerTodosUsuarios();
    
    // Procesar acciones (activar/desactivar/eliminar)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        $usuario_id = $_POST['usuario_id'] ?? '';
        
        switch ($action) {
            case 'activar':
                if ($usuarioModel->activarUsuario($usuario_id)) {
                    $_SESSION['success'] = 'Usuario activado correctamente';
                } else {
                    $_SESSION['error'] = 'Error al activar usuario';
                }
                break;
                
            case 'desactivar':
                if ($usuarioModel->desactivarUsuario($usuario_id)) {
                    $_SESSION['success'] = 'Usuario desactivado correctamente';
                } else {
                    $_SESSION['error'] = 'Error al desactivar usuario';
                }
                break;
                
            case 'eliminar':
                if ($usuarioModel->eliminarUsuario($usuario_id)) {
                    $_SESSION['success'] = 'Usuario eliminado correctamente';
                } else {
                    $_SESSION['error'] = 'Error al eliminar usuario';
                }
                break;
        }
        
        $this->redirect('admin/usuarios');
    }
    
    $data = [
        'titulo' => 'Gestión de Usuarios - Admin',
        'usuarios' => $usuarios
    ];
    
    $this->view('admin/usuarios', $data);
}
public function productos() {
    $productoModel = $this->model('ProductoModel');
    
    // Obtener todos los productos
    $productos = $productoModel->obtenerTodosProductos();
    
    // Procesar acciones (crear/editar/eliminar/activar/desactivar)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'crear':
                $datos = [
                    'nombre' => trim($_POST['nombre']),
                    'categoria' => trim($_POST['categoria']),
                    'precio' => floatval($_POST['precio']),
                    'stock' => intval($_POST['stock']),
                    'disponible' => isset($_POST['disponible']) ? 1 : 0
                ];
                
                if ($productoModel->crearProducto($datos)) {
                    $_SESSION['success'] = 'Producto creado correctamente';
                } else {
                    $_SESSION['error'] = 'Error al crear producto';
                }
                break;
                
            case 'editar':
                $id = $_POST['id'];
                $datos = [
                    'nombre' => trim($_POST['nombre']),
                    'categoria' => trim($_POST['categoria']),
                    'precio' => floatval($_POST['precio']),
                    'stock' => intval($_POST['stock']),
                    'disponible' => isset($_POST['disponible']) ? 1 : 0
                ];
                
                if ($productoModel->actualizarProducto($id, $datos)) {
                    $_SESSION['success'] = 'Producto actualizado correctamente';
                } else {
                    $_SESSION['error'] = 'Error al actualizar producto';
                }
                break;
                
            case 'eliminar':
                $id = $_POST['id'];
                if ($productoModel->eliminarProducto($id)) {
                    $_SESSION['success'] = 'Producto eliminado correctamente';
                } else {
                    $_SESSION['error'] = 'Error al eliminar producto';
                }
                break;
                
            case 'activar':
                $id = $_POST['id'];
                if ($productoModel->activarProducto($id)) {
                    $_SESSION['success'] = 'Producto activado correctamente';
                } else {
                    $_SESSION['error'] = 'Error al activar producto';
                }
                break;
                
            case 'desactivar':
                $id = $_POST['id'];
                if ($productoModel->desactivarProducto($id)) {
                    $_SESSION['success'] = 'Producto desactivado correctamente';
                } else {
                    $_SESSION['error'] = 'Error al desactivar producto';
                }
                break;
        }
        
        $this->redirect('admin/productos');
    }
    
    $data = [
        'titulo' => 'Gestión de Productos - Admin',
        'productos' => $productos,
        'categorias' => $productoModel->obtenerCategorias()
    ];
    
    $this->view('admin/productos', $data);
}
public function reportes() {
    $pedidoModel = $this->model('PedidoModel');
    $productoModel = $this->model('ProductoModel');
    
    // Obtener parámetros de filtrado
    $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
    $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
    $tipo_reporte = $_GET['tipo_reporte'] ?? 'ventas';
    
    $reporte_data = [];
    
    switch ($tipo_reporte) {
        case 'ventas':
            $reporte_data = $pedidoModel->obtenerReporteVentas($fecha_inicio, $fecha_fin);
            break;
            
        case 'productos':
            $reporte_data = $productoModel->obtenerReporteProductos($fecha_inicio, $fecha_fin);
            break;
            
        case 'usuarios':
            $usuarioModel = $this->model('UsuarioModel');
            $reporte_data = $usuarioModel->obtenerReporteUsuarios($fecha_inicio, $fecha_fin);
            break;
            
        case 'inventario':
            $reporte_data = $productoModel->obtenerReporteInventario();
            break;
    }
    
    // Procesar exportación si se solicita
    if (isset($_GET['exportar'])) {
        $this->exportarReporte($reporte_data, $tipo_reporte, $fecha_inicio, $fecha_fin);
        return;
    }
    
    $data = [
        'titulo' => 'Reportes - Admin',
        'reporte_data' => $reporte_data,
        'fecha_inicio' => $fecha_inicio,
        'fecha_fin' => $fecha_fin,
        'tipo_reporte' => $tipo_reporte,
        'tipos_reporte' => [
            'ventas' => 'Ventas',
            'productos' => 'Productos más vendidos',
            'usuarios' => 'Actividad de usuarios',
            'inventario' => 'Estado de inventario'
        ]
    ];
    
    $this->view('admin/reportes', $data);
}

private function exportarReporte($data, $tipo_reporte, $fecha_inicio, $fecha_fin) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=reporte_' . $tipo_reporte . '_' . $fecha_inicio . '_' . $fecha_fin . '.csv');
    
    $output = fopen('php://output', 'w');
    
    // Encabezados según el tipo de reporte
    switch ($tipo_reporte) {
        case 'ventas':
            fputcsv($output, ['Fecha', 'Total Ventas', 'Número de Pedidos', 'Promedio por Pedido']);
            foreach ($data as $fila) {
                fputcsv($output, [
                    $fila['fecha'],
                    number_format($fila['total_ventas'], 2),
                    $fila['numero_pedidos'],
                    number_format($fila['promedio_pedido'], 2)
                ]);
            }
            break;
            
        case 'productos':
            fputcsv($output, ['Producto', 'Categoría', 'Cantidad Vendida', 'Total Vendido']);
            foreach ($data as $fila) {
                fputcsv($output, [
                    $fila['nombre'],
                    $fila['categoria'],
                    $fila['cantidad_vendida'],
                    number_format($fila['total_vendido'], 2)
                ]);
            }
            break;
            
        case 'usuarios':
            fputcsv($output, ['Usuario', 'Email', 'Pedidos Realizados', 'Total Gastado']);
            foreach ($data as $fila) {
                fputcsv($output, [
                    $fila['nombre'],
                    $fila['email'],
                    $fila['pedidos_realizados'],
                    number_format($fila['total_gastado'], 2)
                ]);
            }
            break;
            
        case 'inventario':
            fputcsv($output, ['Producto', 'Categoría', 'Stock Actual', 'Precio Unitario', 'Valor Total']);
            foreach ($data as $fila) {
                fputcsv($output, [
                    $fila['nombre'],
                    $fila['categoria'],
                    $fila['stock'],
                    number_format($fila['precio'], 2),
                    number_format($fila['stock'] * $fila['precio'], 2)
                ]);
            }
            break;
    }
    
    fclose($output);
    exit;
}
public function inventario() {
    $inventarioModel = $this->model('InventarioModel');
    
    // Obtener todo el inventario
    $inventario = $inventarioModel->obtenerInventarioCompleto();
    
    // Procesar acciones (actualizar stock, agregar producto, etc.)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'actualizar':
                $producto_id = $_POST['producto_id'];
                $cantidad = intval($_POST['cantidad']);
                $minimo = intval($_POST['minimo']);
                
                if ($inventarioModel->actualizarStock($producto_id, $cantidad, $minimo)) {
                    $_SESSION['success'] = 'Stock actualizado correctamente';
                } else {
                    $_SESSION['error'] = 'Error al actualizar stock';
                }
                break;
                
            case 'agregar':
                $datos = [
                    'producto' => trim($_POST['producto']),
                    'cantidad_actual' => intval($_POST['cantidad_actual']),
                    'minimo' => intval($_POST['minimo'])
                ];
                
                if ($inventarioModel->agregarProductoInventario($datos)) {
                    $_SESSION['success'] = 'Producto agregado al inventario';
                } else {
                    $_SESSION['error'] = 'Error al agregar producto';
                }
                break;
                
            case 'eliminar':
                $id = $_POST['id'];
                if ($inventarioModel->eliminarProductoInventario($id)) {
                    $_SESSION['success'] = 'Producto eliminado del inventario';
                } else {
                    $_SESSION['error'] = 'Error al eliminar producto';
                }
                break;
                
            case 'generar_alerta':
                $productos_bajos = $inventarioModel->obtenerProductosStockBajo();
                if (!empty($productos_bajos)) {
                    $_SESSION['info'] = 'Alertas generadas para ' . count($productos_bajos) . ' productos con stock bajo';
                } else {
                    $_SESSION['success'] = 'No hay productos con stock bajo';
                }
                break;
        }
        
        $this->redirect('admin/inventario');
    }
    
    // Obtener productos con stock bajo para alertas
    $productos_bajos = $inventarioModel->obtenerProductosStockBajo();
    
    $data = [
        'titulo' => 'Gestión de Inventario - Admin',
        'inventario' => $inventario,
        'productos_bajos' => $productos_bajos,
        'total_valor_inventario' => $inventarioModel->calcularValorTotalInventario()
    ];
    
    $this->view('admin/inventario', $data);
}
}

?>