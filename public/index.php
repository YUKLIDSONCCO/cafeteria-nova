<?php
require_once '../config/config.php';

// Manejo de errores
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

require_once '../core/ErrorHandler.php';
require_once '../core/Router.php';

try {
    $router = new Router();
} catch (Exception $e) {
    ErrorHandler::handleException($e);
}