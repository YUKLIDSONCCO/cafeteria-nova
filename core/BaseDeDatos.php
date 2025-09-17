<?php
class BaseDeDatos {
    private static $instancia = null;
    private $pdo;

    private function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public static function getConexion() {
        if (self::$instancia === null) {
            self::$instancia = new BaseDeDatos();
        }
        return self::$instancia->pdo;
    }
}
