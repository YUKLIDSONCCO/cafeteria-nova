<?php
require_once '../config/database.php';

class ClienteModel {
    private $db;
    private $table = 'productos';

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function obtenerProductos() {
        $query = "SELECT * FROM " . $this->table . " WHERE disponible = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function crearPedido($datos) {
        // Lógica para crear pedido
        $query = "INSERT INTO pedidos SET codigo=:codigo, cliente_id=:cliente_id, tipo=:tipo, estado=:estado, total=:total";
        $stmt = $this->db->prepare($query);
        
        // Generar código único
        $codigo = 'PED' . uniqid();
        
        $stmt->bindParam(":codigo", $codigo);
        $stmt->bindParam(":cliente_id", $datos['cliente_id']);
        $stmt->bindParam(":tipo", $datos['tipo']);
        $stmt->bindParam(":estado", $datos['estado']);
        $stmt->bindParam(":total", $datos['total']);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
}
?>