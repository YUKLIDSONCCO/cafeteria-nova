<?php
// models/MesaModel.php
// Modelo mínimo para manejar mesas: obtenerMesas() y asignarMesa()
// Adaptar la conexión si usas una clase BaseDeDatos/Modelo en tu proyecto.

class MesaModel {
    private $pdo;

    public function __construct($pdo = null) {
        if ($pdo) {
            $this->pdo = $pdo;
        } else {
            // Fallback: conexión PDO local (ajusta constantes si tienes config)
            $host = defined('DB_HOST') ? DB_HOST : '127.0.0.1';
            $db   = defined('DB_NAME') ? DB_NAME : 'cafeteria_nova';
            $user = defined('DB_USER') ? DB_USER : 'root';
            $pass = defined('DB_PASS') ? DB_PASS : '';
            $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";
            try {
                $this->pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } catch (PDOException $e) {
                die("DB connection failed (MesaModel): " . $e->getMessage());
            }
        }
    }

    // Obtener todas las mesas
    public function obtenerMesas() {
        $stmt = $this->pdo->prepare("SELECT id, codigo, capacidad, estado FROM mesas ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Asignar mesa a un pedido y marcar mesa como ocupada
    // Suponemos que en la tabla pedidos hay una columna mesa_id opcional.
    public function asignarMesa($pedido_id, $mesa_id) {
        try {
            $this->pdo->beginTransaction();

            // Opcional: actualizar pedido con mesa_id (si existe columna)
            $hasMesaCol = $this->existeColumna('pedidos', 'mesa_id');
            if ($hasMesaCol) {
                $stmt = $this->pdo->prepare("UPDATE pedidos SET cliente_id = cliente_id, /* no touch */ mesa_id = :mesa WHERE id = :pedido");
                // si la columna existe, hacemos una actualización real:
                $stmt = $this->pdo->prepare("UPDATE pedidos SET mesa_id = :mesa WHERE id = :pedido");
                $stmt->execute([':mesa' => $mesa_id, ':pedido' => $pedido_id]);
            }

            // Marcar mesa como ocupada
            $stmt2 = $this->pdo->prepare("UPDATE mesas SET estado = 'ocupada' WHERE id = :mesa");
            $stmt2->execute([':mesa' => $mesa_id]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error asignarMesa: " . $e->getMessage());
            return false;
        }
    }

    // Liberar mesa (útil cuando pedido entregado)
    public function liberarMesa($mesa_id) {
        $stmt = $this->pdo->prepare("UPDATE mesas SET estado = 'libre' WHERE id = :mesa");
        return $stmt->execute([':mesa' => $mesa_id]);
    }

    // Helper: chequear si columna existe (para adaptarse a esquema)
    private function existeColumna($tabla, $columna) {
        $stmt = $this->pdo->prepare("SHOW COLUMNS FROM `{$tabla}` LIKE :col");
        $stmt->execute([':col' => $columna]);
        return (bool)$stmt->fetch();
    }
}
