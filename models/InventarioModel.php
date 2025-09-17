<?php
require_once '../config/database.php';

class InventarioModel {
    private $db;
    private $table = 'inventario';

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function obtenerInventarioCompleto() {
        try {
            $query = "SELECT i.*, p.precio, p.categoria 
                     FROM " . $this->table . " i 
                     LEFT JOIN productos p ON i.producto = p.nombre 
                     ORDER BY i.producto";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            error_log("Error al obtener inventario: " . $exception->getMessage());
            return [];
        }
    }

    public function obtenerProductosStockBajo() {
        try {
            $query = "SELECT i.*, p.precio, p.categoria 
                     FROM " . $this->table . " i 
                     LEFT JOIN productos p ON i.producto = p.nombre 
                     WHERE i.cantidad_actual <= i.minimo 
                     ORDER BY i.producto";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            error_log("Error al obtener productos con stock bajo: " . $exception->getMessage());
            return [];
        }
    }

    public function actualizarStock($id, $cantidad, $minimo) {
        try {
            $query = "UPDATE " . $this->table . " 
                     SET cantidad_actual = :cantidad, 
                         minimo = :minimo, 
                         actualizado_en = NOW() 
                     WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":cantidad", $cantidad);
            $stmt->bindParam(":minimo", $minimo);
            $stmt->bindParam(":id", $id);
            return $stmt->execute();
        } catch(PDOException $exception) {
            error_log("Error al actualizar stock: " . $exception->getMessage());
            return false;
        }
    }

    public function agregarProductoInventario($datos) {
        try {
            $query = "INSERT INTO " . $this->table . " 
                     (producto, cantidad_actual, minimo) 
                     VALUES (:producto, :cantidad_actual, :minimo)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":producto", $datos['producto']);
            $stmt->bindParam(":cantidad_actual", $datos['cantidad_actual']);
            $stmt->bindParam(":minimo", $datos['minimo']);
            return $stmt->execute();
        } catch(PDOException $exception) {
            error_log("Error al agregar producto al inventario: " . $exception->getMessage());
            return false;
        }
    }

    public function eliminarProductoInventario($id) {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            return $stmt->execute();
        } catch(PDOException $exception) {
            error_log("Error al eliminar producto del inventario: " . $exception->getMessage());
            return false;
        }
    }

    public function calcularValorTotalInventario() {
        try {
            $query = "SELECT SUM(i.cantidad_actual * COALESCE(p.precio, 0)) as valor_total 
                     FROM " . $this->table . " i 
                     LEFT JOIN productos p ON i.producto = p.nombre";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['valor_total'] ?? 0;
        } catch(PDOException $exception) {
            error_log("Error al calcular valor total del inventario: " . $exception->getMessage());
            return 0;
        }
    }

    public function obtenerMovimientosInventario($fecha_inicio = null, $fecha_fin = null) {
        try {
            $query = "SELECT * FROM logs_eventos 
                     WHERE evento_tipo LIKE '%inventario%'";
            
            if ($fecha_inicio && $fecha_fin) {
                $query .= " AND timestamp BETWEEN :fecha_inicio AND :fecha_fin";
            }
            
            $query .= " ORDER BY timestamp DESC";
            
            $stmt = $this->db->prepare($query);
            
            if ($fecha_inicio && $fecha_fin) {
                $stmt->bindParam(":fecha_inicio", $fecha_inicio);
                $stmt->bindParam(":fecha_fin", $fecha_fin);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            error_log("Error al obtener movimientos de inventario: " . $exception->getMessage());
            return [];
        }
    }

    public function buscarProducto($termino) {
        try {
            $query = "SELECT i.*, p.precio, p.categoria 
                     FROM " . $this->table . " i 
                     LEFT JOIN productos p ON i.producto = p.nombre 
                     WHERE i.producto LIKE :termino 
                     ORDER BY i.producto";
            $stmt = $this->db->prepare($query);
            $termino = "%" . $termino . "%";
            $stmt->bindParam(":termino", $termino);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            error_log("Error al buscar producto: " . $exception->getMessage());
            return [];
        }
    }

    public function obtenerProductoPorId($id) {
        try {
            $query = "SELECT i.*, p.precio, p.categoria 
                     FROM " . $this->table . " i 
                     LEFT JOIN productos p ON i.producto = p.nombre 
                     WHERE i.id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            error_log("Error al obtener producto por ID: " . $exception->getMessage());
            return false;
        }
    }

    public function actualizarDesdePedido($producto_id, $cantidad_utilizada) {
        try {
            $query = "UPDATE " . $this->table . " 
                     SET cantidad_actual = cantidad_actual - :cantidad 
                     WHERE id = :id AND cantidad_actual >= :cantidad";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":cantidad", $cantidad_utilizada);
            $stmt->bindParam(":id", $producto_id);
            return $stmt->execute();
        } catch(PDOException $exception) {
            error_log("Error al actualizar inventario desde pedido: " . $exception->getMessage());
            return false;
        }
    }
}
?>