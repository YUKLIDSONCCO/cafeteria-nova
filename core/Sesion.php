<?php
class Sesion {
    public static function iniciarSesion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function verificarAutenticacion() {
        self::iniciarSesion();
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit();
        }
    }
    
    public static function verificarRol($rolPermitido) {
        self::verificarAutenticacion();
        
        if ($_SESSION['usuario_rol'] !== $rolPermitido) {
            header('Location: ' . BASE_URL . 'auth/accesoDenegado');
            exit();
        }
    }
    
    public static function obtenerUsuario() {
        self::iniciarSesion();
        return [
            'id' => $_SESSION['usuario_id'] ?? null,
            'nombre' => $_SESSION['usuario_nombre'] ?? null,
            'email' => $_SESSION['usuario_email'] ?? null,
            'rol' => $_SESSION['usuario_rol'] ?? null
        ];
    }
    
    public static function destruirSesion() {
        self::iniciarSesion();
        session_unset();
        session_destroy();
        session_write_close();
    }
}
?>