<?php
// Configuración de la aplicación
define('BASE_URL', 'http://192.168.18.21/cafeteria-nova/public/');

define('DB_HOST', '192.168.18.21');
define('DB_NAME', 'cafeteria_nova');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuración de roles
define('ROLES', [
    'cliente' => 'cliente',
    'mesero' => 'mesero', 
    'barista' => 'barista',
    'cajero' => 'cajero',
    'administrador' => 'administrador'
]);

// Configuración de entorno
define('ENVIRONMENT', 'development');
?>