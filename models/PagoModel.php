<?php
require_once '../config/database.php';

class PagoModel {
    // Método obtenerPagosPendientes ya está definido, eliminar duplicidad
    private $db;
    private $table = 'pagos';

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function crearPago($pedido_id, $monto, $metodo) {
        try {
            $this->db->beginTransaction();

            // Crear pago
            $query = "INSERT INTO " . $this->table . " (pedido_id, monto, metodo, estado) 
                     VALUES (:pedido_id, :monto, :metodo, 'pendiente')";
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(":pedido_id", $pedido_id);
            $stmt->bindParam(":monto", $monto);
            $stmt->bindParam(":metodo", $metodo);
            $stmt->execute();

            // Actualizar estado del pedido
            $pedidoQuery = "UPDATE pedidos SET estado = 'pagado' WHERE id = :pedido_id";
            $pedidoStmt = $this->db->prepare($pedidoQuery);
            $pedidoStmt->bindParam(":pedido_id", $pedido_id);
            $pedidoStmt->execute();

            $this->db->commit();
            return true;

        } catch(PDOException $exception) {
            $this->db->rollBack();
            error_log("Error al crear pago: " . $exception->getMessage());
            return false;
        }
    }

    public function obtenerTotalHoy() {
        try {
            $query = "SELECT SUM(monto) as total FROM " . $this->table . " 
                     WHERE DATE(timestamp) = CURDATE() AND estado = 'completado'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch(PDOException $exception) {
            error_log("Error al obtener total: " . $exception->getMessage());
            return 0;
        }
    }

    public function obtenerVentasDelDia() {
        try {
            $query = "SELECT p.*, ped.codigo as pedido_codigo 
                     FROM " . $this->table . " p 
                     JOIN pedidos ped ON p.pedido_id = ped.id 
                     WHERE DATE(p.timestamp) = CURDATE() 
                     ORDER BY p.timestamp DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            error_log("Error al obtener ventas: " . $exception->getMessage());
            return [];
        }
    }

    public function obtenerTotalesPorMetodo() {
        try {
            $query = "SELECT metodo, SUM(monto) as total 
                     FROM " . $this->table . " 
                     WHERE DATE(timestamp) = CURDATE() AND estado = 'completado'
                     GROUP BY metodo";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            error_log("Error al obtener totales por método: " . $exception->getMessage());
            return [];
        }
    }

    public function actualizarEstadoPago($pago_id, $estado) {
        try {
            $query = "UPDATE pagos SET estado = :estado WHERE id = :pago_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":estado", $estado);
            $stmt->bindParam(":pago_id", $pago_id);
            return $stmt->execute();
        } catch(PDOException $exception) {
            error_log("Error al actualizar estado de pago: " . $exception->getMessage());
            return false;
        }
    }

    public function obtenerPagosPendientes() {
        try {
            $query = "SELECT * FROM pagos WHERE estado = 'pendiente' ORDER BY timestamp DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            error_log("Error al obtener pagos pendientes: " . $exception->getMessage());
            return [];
        }
    }
}
?>
