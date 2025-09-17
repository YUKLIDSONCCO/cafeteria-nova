<?php
class Router {
    private $controller = 'Cliente';
    private $method = 'index';
    private $params = [];

    public function __construct() {
        $this->dispatch();
    }
    
    public function dispatch() {
        $url = $this->parseUrl();
        
        // Determinar controlador
        if (isset($url[0]) && !empty($url[0])) {
            $controllerName = ucfirst($url[0]);
            $controllerFile = '../controllers/' . $controllerName . 'Controller.php';
            
            if (file_exists($controllerFile)) {
                $this->controller = $controllerName;
                unset($url[0]);
            } else {
                // Si no existe el controlador, usar Cliente por defecto
                $this->controller = 'Cliente';
            }
        }
        
        // Incluir y crear instancia del controlador
        require_once '../controllers/' . $this->controller . 'Controller.php';
        $controllerClass = $this->controller . 'Controller';
        
        if (!class_exists($controllerClass)) {
            die("Error: La clase $controllerClass no existe.");
        }
        
        $this->controller = new $controllerClass();
        
        // Determinar método
        if (isset($url[1])) {
            $methodName = $url[1];
            if (method_exists($this->controller, $methodName)) {
                $this->method = $methodName;
                unset($url[1]);
            }
        }
        
        // Obtener parámetros
        $this->params = $url ? array_values($url) : [];
        
        // Llamar al controlador y método
        call_user_func_array([$this->controller, $this->method], $this->params);
    }
    
    private function parseUrl() {
        if (isset($_GET['url'])) {
            $url = trim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
        return [];
    }
}
?>