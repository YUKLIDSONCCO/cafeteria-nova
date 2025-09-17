<?php
class Vista {
    public static function render($view, $data = []) {
        extract($data);
        require_once "../views/templates/header.php";
        require_once "../views/$view.php";
        require_once "../views/templates/footer.php";
    }
}
