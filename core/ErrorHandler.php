<?php
class ErrorHandler {
    public static function handleException($exception) {
        if (ENVIRONMENT === 'development') {
            echo "<h2>Error:</h2>";
            echo "<p>" . $exception->getMessage() . "</p>";
            echo "<pre>" . $exception->getTraceAsString() . "</pre>";
        } else {
            error_log("Error: " . $exception->getMessage());
            echo "<h2>Ocurrió un error inesperado</h2>";
            echo "<p>Por favor, intente más tarde.</p>";
        }
    }
    
    public static function handleError($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            return false;
        }
        
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}

// Configurar manejadores de errores
set_error_handler([ErrorHandler::class, 'handleError']);
set_exception_handler([ErrorHandler::class, 'handleException']);
?>