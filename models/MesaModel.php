<?php
require_once '../config/database.php';

class MesaModel {
    private $db;
    private $table = 'mesas';

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function obtenerMesas() {
        try {
            $query = "SELECT * FROM " . $this->table . " ORDER BY codigo";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            error_log("Error al obtener mesas: " . $exception->getMessage());
            return false;
        }
    }

    public function asignarMesa($pedido_id, $mesa_id) {
        try {
            // Actualizar estado de la mesa
            $query = "UPDATE " . $this->table . " SET estado = 'ocupada' WHERE id = :mesa_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":mesa_id", $mesa_id);
            $stmt->execute();

            // Aquí podrías guardar la relación pedido-mesa en otra tabla si es necesario
            return true;
        } catch(PDOException $exception) {
            error_log("Error al asignar mesa: " . $exception->getMessage());
            return false;
        }
    }

    public function liberarMesa($mesa_id) {
        try {
            $query = "UPDATE " . $this->table . " SET estado = 'libre' WHERE id = :mesa_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":mesa_id", $mesa_id);
            return $stmt->execute();
        } catch(PDOException $exception) {
            error_log("Error al liberar mesa: " . $exception->getMessage());
            return false;
        }
    }
}
?>