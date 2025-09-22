<?php
require_once '../config/database.php';

class PedidoModel {
    private $db;
    private $table = 'pedidos';

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function crearPedido($data) {
    try {
        $this->db->beginTransaction();

        // Insertar pedido principal
        $stmt = $this->db->prepare("INSERT INTO pedidos (cliente_id, nombre_cliente, tipo, total, estado, creado_en) 
                                    VALUES (:cliente_id, :nombre_cliente, :tipo, :total, 'creado', NOW())");
        $stmt->bindParam(':cliente_id', $data['cliente_id']);
        $stmt->bindParam(':nombre_cliente', $data['nombre_cliente']);
        $stmt->bindParam(':tipo', $data['tipo']);
        $stmt->bindParam(':total', $data['total']);
        $stmt->execute();

        // ✅ Ahora sí obtenemos el ID generado
        $pedido_id = $this->db->lastInsertId();

        // Insertar productos en detalle_pedido
        if (!empty($data['productos'])) {
            $stmtDetalle = $this->db->prepare("INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio_unit) 
                                               VALUES (:pedido_id, :producto_id, :cantidad, :precio_unit)");
            foreach ($data['productos'] as $producto) {
                $stmtDetalle->execute([
                    ':pedido_id'   => $pedido_id,
                    ':producto_id' => $producto['id'],
                    ':cantidad'    => $producto['cantidad'],
                    ':precio_unit' => $producto['precio']
                ]);
            }
        }

        $this->db->commit();
        return $pedido_id;

    } catch (PDOException $e) {
        $this->db->rollBack();
        error_log("Error en crearPedido: " . $e->getMessage());
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
        $sql = "SELECT 
                    p.id,
                    COALESCE(p.codigo, CONCAT('ORD', LPAD(p.id, 4, '0'))) as codigo,
                    COALESCE(p.nombre_cliente, 'Cliente') as cliente_nombre,
                    p.tipo,
                    p.estado,
                    p.total,
                    p.creado_en
                FROM pedidos p
                WHERE p.estado IN ('listo', 'preparacion', 'creado')
                ORDER BY p.creado_en ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error en obtenerPedidosParaPago: " . $e->getMessage());
        return [];
    }
}
public function obtenerReporteVentas($fecha_inicio, $fecha_fin) {
    try {
        $sql = "SELECT 
                    DATE(creado_en) as fecha,
                    COUNT(*) as numero_pedidos,
                    COALESCE(SUM(total), 0) as total_ventas,
                    COALESCE(AVG(total), 0) as promedio_pedido
                FROM pedidos 
                WHERE estado IN ('pagado', 'entregado')
                AND DATE(creado_en) BETWEEN ? AND ?
                GROUP BY DATE(creado_en)
                ORDER BY fecha DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$fecha_inicio, $fecha_fin]);
        
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Si no hay datos, devolver array vacío
        if (!$resultado) {
            return [];
        }
        
        return $resultado;
        
    } catch (PDOException $e) {
        error_log("Error en obtenerReporteVentas: " . $e->getMessage());
        return [];
    }
}


public function actualizarMesa($pedido_id, $mesa_id) {
    $sql = "UPDATE pedidos SET mesa_id = ? WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$mesa_id, $pedido_id]);
}

}

