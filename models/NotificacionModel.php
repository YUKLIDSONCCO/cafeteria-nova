<?php
require_once '../config/database.php';

class NotificacionModel {
    private $db;
    private $table = 'notificaciones';

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function crear($tipo, $destinatario_rol, $mensaje, $pedido_id = null) {
        try {
            $query = "INSERT INTO " . $this->table . " (tipo, destinatario_rol, mensaje, pedido_id) 
                     VALUES (:tipo, :destinatario_rol, :mensaje, :pedido_id)";
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(":tipo", $tipo);
            $stmt->bindParam(":destinatario_rol", $destinatario_rol);
            $stmt->bindParam(":mensaje", $mensaje);
            $stmt->bindParam(":pedido_id", $pedido_id);
            
            return $stmt->execute();
        } catch(PDOException $exception) {
            error_log("Error al crear notificación: " . $exception->getMessage());
            return false;
        }
    }

    public function obtenerPendientes($rol, $ultima_id = 0) {
        try {
            $query = "SELECT * FROM " . $this->table . " 
                     WHERE (destinatario_rol = :rol OR destinatario_rol IS NULL) 
                     AND id > :ultima_id AND leido = FALSE 
                     ORDER BY creado_en DESC";
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(":rol", $rol);
            $stmt->bindParam(":ultima_id", $ultima_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            error_log("Error al obtener notificaciones: " . $exception->getMessage());
            return [];
        }
    }

    public function marcarLeida($id) {
        try {
            $query = "UPDATE " . $this->table . " SET leido = TRUE WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            return $stmt->execute();
        } catch(PDOException $exception) {
            error_log("Error al marcar notificación como leída: " . $exception->getMessage());
            return false;
        }
    }

    public function marcarTodasLeidas($rol) {
        try {
            $query = "UPDATE " . $this->table . " SET leido = TRUE 
                     WHERE destinatario_rol = :rol AND leido = FALSE";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":rol", $rol);
            return $stmt->execute();
        } catch(PDOException $exception) {
            error_log("Error al marcar notificaciones como leídas: " . $exception->getMessage());
            return false;
        }
    }

    public function contarPendientes($rol) {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                     WHERE (destinatario_rol = :rol OR destinatario_rol IS NULL) 
                     AND leido = FALSE";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":rol", $rol);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch(PDOException $exception) {
            error_log("Error al contar notificaciones: " . $exception->getMessage());
            return 0;
        }
    }
}
?>