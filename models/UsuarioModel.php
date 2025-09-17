<?php
require_once '../config/database.php';

class UsuarioModel {
    private $db;
    private $table = 'usuarios';

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

public function login($email, $password) {
    try {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        if ($stmt->rowCount() == 1) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $usuario['password_hash'])) {
                return $usuario; // incluye campo "activo"
            }
        }
        return false;
    } catch(PDOException $exception) {
        error_log("Error en login: " . $exception->getMessage());
        return false;
    }
}


    public function obtenerPorId($id) {
        try {
            $query = "SELECT id, nombre, email, rol, activo FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            error_log("Error en obtenerPorId: " . $exception->getMessage());
            return false;
        }
    }
    public function obtenerTodosUsuarios() {
    try {
        $query = "SELECT id, nombre, email, rol, activo, creado_en 
                 FROM usuarios 
                 ORDER BY creado_en DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $exception) {
        error_log("Error al obtener usuarios: " . $exception->getMessage());
        return [];
    }
}

public function activarUsuario($id) {
    try {
        $query = "UPDATE usuarios SET activo = 1 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    } catch(PDOException $exception) {
        error_log("Error al activar usuario: " . $exception->getMessage());
        return false;
    }
}

public function desactivarUsuario($id) {
    try {
        $query = "UPDATE usuarios SET activo = 0 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    } catch(PDOException $exception) {
        error_log("Error al desactivar usuario: " . $exception->getMessage());
        return false;
    }
}

public function eliminarUsuario($id) {
    try {
        $query = "DELETE FROM usuarios WHERE id = :id AND rol != 'administrador'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    } catch(PDOException $exception) {
        error_log("Error al eliminar usuario: " . $exception->getMessage());
        return false;
    }
}
public function obtenerReporteUsuarios($fecha_inicio, $fecha_fin) {
    try {
        $query = "SELECT 
                    u.nombre,
                    u.email,
                    COUNT(p.id) as pedidos_realizados,
                    COALESCE(SUM(p.total), 0) as total_gastado
                 FROM usuarios u
                 LEFT JOIN pedidos p ON u.id = p.cliente_id 
                    AND p.estado = 'completado'
                    AND DATE(p.creado_en) BETWEEN :fecha_inicio AND :fecha_fin
                 WHERE u.rol = 'cliente' AND u.activo = 1
                 GROUP BY u.id, u.nombre, u.email
                 ORDER BY total_gastado DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":fecha_inicio", $fecha_inicio);
        $stmt->bindParam(":fecha_fin", $fecha_fin);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $exception) {
        error_log("Error al obtener reporte de usuarios: " . $exception->getMessage());
        return [];
    }
}
// Agrega estos métodos a tu clase UsuarioModel existente

public function getUserByEmail($email) {
    try {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $exception) {
        error_log("Error en getUserByEmail: " . $exception->getMessage());
        return false;
    }
}

public function crearUsuario($userData) {
    try {
        $query = "INSERT INTO " . $this->table . " 
                 (nombre, email, email_recuperacion, password_hash, rol, activo) 
                 VALUES (:nombre, :email, :email_recuperacion, :password_hash, :rol, :activo)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":nombre", $userData['nombre']);
        $stmt->bindParam(":email", $userData['email']);
        $stmt->bindParam(":email_recuperacion", $userData['email_recuperacion']);
        $stmt->bindParam(":password_hash", $userData['password_hash']);
        $stmt->bindParam(":rol", $userData['rol']);
        $stmt->bindParam(":activo", $userData['activo']);
        
        return $stmt->execute();
    } catch(PDOException $exception) {
        error_log("Error en crearUsuario: " . $exception->getMessage());
        return false;
    }
}

public function guardarTokenRecuperacion($usuarioId, $token, $expira) {
    $sql = "INSERT INTO tokens_recuperacion (usuario_id, token, expira_en) VALUES (?, ?, ?)";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$usuarioId, $token, $expira]);
}

public function getUserByToken($token) {
    $sql = "SELECT u.* 
            FROM usuarios u
            INNER JOIN tokens_recuperacion t ON u.id = t.usuario_id
            WHERE t.token = ? AND t.expira_en > NOW()
            LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$token]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function actualizarPassword($id, $newPasswordHash) {
    $sql = "UPDATE usuarios SET password_hash = ? WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$newPasswordHash, $id]);
}



}
?>