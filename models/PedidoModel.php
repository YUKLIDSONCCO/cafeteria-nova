<?php
require_once '../config/database.php';

class PedidoModel {
    private $db;
    private $table = 'pedidos';

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function crearPedido($datos) {
        try {
            $this->db->beginTransaction();

            // Crear pedido
            $query = "INSERT INTO pedidos (codigo, cliente_id, tipo, estado, total) 
                     VALUES (:codigo, :cliente_id, :tipo, :estado, :total)";
            $stmt = $this->db->prepare($query);
            
            $codigo = 'PED' . date('YmdHis') . rand(100, 999);
            $cliente_id = $datos['cliente_id'] ?? null;
            $tipo = $datos['tipo'];
            $estado = 'creado';
            $total = $datos['total'];

            $stmt->bindParam(":codigo", $codigo);
            $stmt->bindParam(":cliente_id", $cliente_id);
            $stmt->bindParam(":tipo", $tipo);
            $stmt->bindParam(":estado", $estado);
            $stmt->bindParam(":total", $total);
            
            $stmt->execute();
            $pedido_id = $this->db->lastInsertId();

            // Crear detalles del pedido
            foreach ($datos['productos'] as $producto) {
                $this->agregarDetallePedido($pedido_id, $producto['id'], $producto['cantidad'], $producto['precio']);
            }

            $this->db->commit();
            return $pedido_id;

        } catch(PDOException $exception) {
            $this->db->rollBack();
            error_log("Error al crear pedido: " . $exception->getMessage());
            return false;
        }
    }

    private function agregarDetallePedido($pedido_id, $producto_id, $cantidad, $precio) {
        $query = "INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio_unit) 
                 VALUES (:pedido_id, :producto_id, :cantidad, :precio)";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(":pedido_id", $pedido_id);
        $stmt->bindParam(":producto_id", $producto_id);
        $stmt->bindParam(":cantidad", $cantidad);
        $stmt->bindParam(":precio", $precio);
        
        return $stmt->execute();
    }

    public function obtenerPedidosPorEstado($estado) {
        try {
            $query = "SELECT p.*, u.nombre as cliente_nombre 
                     FROM pedidos p 
                     LEFT JOIN usuarios u ON p.cliente_id = u.id 
                     WHERE p.estado = :estado 
                     ORDER BY p.creado_en DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":estado", $estado);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            error_log("Error al obtener pedidos: " . $exception->getMessage());
            return false;
        }
    }

    public function actualizarEstado($pedido_id, $estado) {
        try {
            $query = "UPDATE pedidos SET estado = :estado WHERE id = :pedido_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":estado", $estado);
            $stmt->bindParam(":pedido_id", $pedido_id);
            return $stmt->execute();
        } catch(PDOException $exception) {
            error_log("Error al actualizar estado: " . $exception->getMessage());
            return false;
        }
    }

    public function obtenerPedidoPorId($id) {
        try {
            $query = "SELECT p.*, u.nombre as cliente_nombre 
                     FROM pedidos p 
                     LEFT JOIN usuarios u ON p.cliente_id = u.id 
                     WHERE p.id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            error_log("Error al obtener pedido: " . $exception->getMessage());
            return false;
        }
    }

    public function obtenerDetallesPedido($pedido_id) {
        try {
            $query = "SELECT dp.*, p.nombre as producto_nombre, p.categoria 
                     FROM detalle_pedido dp 
                     JOIN productos p ON dp.producto_id = p.id 
                     WHERE dp.pedido_id = :pedido_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":pedido_id", $pedido_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            error_log("Error al obtener detalles: " . $exception->getMessage());
            return false;
        }
    }
    public function obtenerTodosPedidos() {
    try {
        $query = "SELECT p.*, u.nombre as cliente_nombre 
                 FROM pedidos p 
                 LEFT JOIN usuarios u ON p.cliente_id = u.id 
                 ORDER BY p.creado_en DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $exception) {
        error_log("Error al obtener todos los pedidos: " . $exception->getMessage());
        return false;
    }
}

public function obtenerPedidosParaPago() {
    try {
        $query = "SELECT p.*, u.nombre as cliente_nombre 
                 FROM pedidos p 
                 LEFT JOIN usuarios u ON p.cliente_id = u.id 
                 WHERE p.estado IN ('listo', 'entregado') 
                 ORDER BY p.creado_en DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $exception) {
        error_log("Error al obtener pedidos para pago: " . $exception->getMessage());
        return false;
    }
}
public function obtenerReporteVentas($fecha_inicio, $fecha_fin) {
    try {
        $query = "SELECT 
                    DATE(p.creado_en) as fecha,
                    COUNT(p.id) as numero_pedidos,
                    SUM(p.total) as total_ventas,
                    AVG(p.total) as promedio_pedido
                 FROM pedidos p 
                 WHERE p.estado = 'completado' 
                 AND DATE(p.creado_en) BETWEEN :fecha_inicio AND :fecha_fin
                 GROUP BY DATE(p.creado_en)
                 ORDER BY fecha DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":fecha_inicio", $fecha_inicio);
        $stmt->bindParam(":fecha_fin", $fecha_fin);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $exception) {
        error_log("Error al obtener reporte de ventas: " . $exception->getMessage());
        return [];
    }
}
}
?>