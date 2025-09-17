<?php
class BaseController {
    protected function model($model) {
        $modelFile = '../models/' . $model . '.php';
        
        if (file_exists($modelFile)) {
            require_once $modelFile;
            
            // Verificar que la clase existe
            if (class_exists($model)) {
                return new $model();
            } else {
                die("Error: La clase $model no existe en el archivo.");
            }
        } else {
            die("Error: El archivo del modelo $model no existe.");
        }
    }
    
    protected function view($view, $data = []) {
        $viewFile = '../views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            // Extraer los datos para que estén disponibles en la vista
            extract($data);
            
            require_once '../views/templates/header.php';
            require_once $viewFile;
            require_once '../views/templates/footer.php';
        } else {
            die("Error: La vista '$view' no existe.");
        }
    }
    
    protected function redirect($url) {
        header('Location: ' . BASE_URL . $url);
        exit();
    }
    
    // Método para cargar helpers si es necesario
    protected function helper($helper) {
        $helperFile = '../helpers/' . $helper . '.php';
        if (file_exists($helperFile)) {
            require_once $helperFile;
        }
    }
}
?>