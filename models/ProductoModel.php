<?php
require_once '../config/database.php';

class ProductoModel {
    private $db;
    private $table = 'productos';

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function obtenerProductos() {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE disponible = 1 ORDER BY categoria, nombre";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch(PDOException $exception) {
            error_log("Error en obtenerProductos: " . $exception->getMessage());
            return false;
        }
    }

    public function obtenerProductoPorId($id) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE id = :id AND disponible = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            error_log("Error en obtenerProductoPorId: " . $exception->getMessage());
            return false;
        }
    }

    public function obtenerProductosPorCategoria($categoria) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE categoria = :categoria AND disponible = 1 ORDER BY nombre";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":categoria", $categoria);
            $stmt->execute();
            return $stmt;
        } catch(PDOException $exception) {
            error_log("Error en obtenerProductosPorCategoria: " . $exception->getMessage());
            return false;
        }
    }
    public function obtenerTodosProductos() {
    try {
        $query = "SELECT * FROM productos ORDER BY categoria, nombre";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $exception) {
        error_log("Error al obtener productos: " . $exception->getMessage());
        return [];
    }
}

public function obtenerCategorias() {
    try {
        $query = "SELECT DISTINCT categoria FROM productos ORDER BY categoria";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch(PDOException $exception) {
        error_log("Error al obtener categorías: " . $exception->getMessage());
        return [];
    }
}

public function crearProducto($datos) {
    try {
        $query = "INSERT INTO productos (nombre, categoria, precio, stock, disponible) 
                 VALUES (:nombre, :categoria, :precio, :stock, :disponible)";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(":nombre", $datos['nombre']);
        $stmt->bindParam(":categoria", $datos['categoria']);
        $stmt->bindParam(":precio", $datos['precio']);
        $stmt->bindParam(":stock", $datos['stock']);
        $stmt->bindParam(":disponible", $datos['disponible']);
        
        return $stmt->execute();
    } catch(PDOException $exception) {
        error_log("Error al crear producto: " . $exception->getMessage());
        return false;
    }
}

public function actualizarProducto($id, $datos) {
    try {
        $query = "UPDATE productos SET 
                 nombre = :nombre, 
                 categoria = :categoria, 
                 precio = :precio, 
                 stock = :stock, 
                 disponible = :disponible 
                 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(":nombre", $datos['nombre']);
        $stmt->bindParam(":categoria", $datos['categoria']);
        $stmt->bindParam(":precio", $datos['precio']);
        $stmt->bindParam(":stock", $datos['stock']);
        $stmt->bindParam(":disponible", $datos['disponible']);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    } catch(PDOException $exception) {
        error_log("Error al actualizar producto: " . $exception->getMessage());
        return false;
    }
}

public function eliminarProducto($id) {
    try {
        $query = "DELETE FROM productos WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    } catch(PDOException $exception) {
        error_log("Error al eliminar producto: " . $exception->getMessage());
        return false;
    }
}

public function activarProducto($id) {
    try {
        $query = "UPDATE productos SET disponible = 1 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    } catch(PDOException $exception) {
        error_log("Error al activar producto: " . $exception->getMessage());
        return false;
    }
}

public function desactivarProducto($id) {
    try {
        $query = "UPDATE productos SET disponible = 0 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    } catch(PDOException $exception) {
        error_log("Error al desactivar producto: " . $exception->getMessage());
        return false;
    }
}
public function obtenerReporteProductos($fecha_inicio, $fecha_fin) {
    try {
        $query = "SELECT 
                    p.nombre,
                    p.categoria,
                    SUM(dp.cantidad) as cantidad_vendida,
                    SUM(dp.cantidad * dp.precio_unit) as total_vendido
                 FROM detalle_pedido dp
                 JOIN pedidos ped ON dp.pedido_id = ped.id
                 JOIN productos p ON dp.producto_id = p.id
                 WHERE ped.estado = 'completado'
                 AND DATE(ped.creado_en) BETWEEN :fecha_inicio AND :fecha_fin
                 GROUP BY p.id, p.nombre, p.categoria
                 ORDER BY total_vendido DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":fecha_inicio", $fecha_inicio);
        $stmt->bindParam(":fecha_fin", $fecha_fin);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $exception) {
        error_log("Error al obtener reporte de productos: " . $exception->getMessage());
        return [];
    }
}

public function obtenerReporteInventario() {
    try {
        $query = "SELECT 
                    nombre,
                    categoria,
                    stock,
                    precio,
                    (stock * precio) as valor_total
                 FROM productos 
                 WHERE disponible = 1
                 ORDER BY categoria, nombre";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $exception) {
        error_log("Error al obtener reporte de inventario: " . $exception->getMessage());
        return [];
    }
}
}
?>