<?php
require_once '../core/BaseController.php';

class AuthController extends BaseController {
    public function __construct() {
        // Verificar si la sesión ya está activa antes de iniciarla
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    public function index() {
    // Redirigir al login por defecto
    $this->redirect('auth/login');
}

    
public function login() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        
        $usuarioModel = $this->model('UsuarioModel');
        $usuario = $usuarioModel->login($email, $password);

        if ($usuario) {
            if ($usuario['activo'] == 0) {
                // 🚫 Redirigir a la vista acceso_denegado
                $this->view('auth/acceso_denegado');
                return;
            }

            // ✅ Usuario activo → guardar sesión
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_rol'] = $usuario['rol'];
            $_SESSION['usuario_email'] = $usuario['email'];

            // Redirigir según rol
            switch ($usuario['rol']) {
                case 'administrador':
                    $this->redirect('admin/dashboard');
                    break;
                case 'mesero':
                    $this->redirect('mesero/dashboard');
                    break;
                case 'barista':
                    $this->redirect('barista/dashboard');
                    break;
                case 'cajero':
                    $this->redirect('cajero/dashboard');
                    break;
                default:
                    $this->redirect('auth/login');
                    break;
            }
        } else {
            // ❌ Credenciales incorrectas
            $_SESSION['error'] = 'Credenciales incorrectas';
            $this->redirect('auth/login');
        }
    } else {
        $this->view('auth/login');
    }
}
// En controllers/AuthController.php - AGREGA ESTE MÉTODO
// En controllers/AuthController.php - REEMPLAZA el método registrar() existente

public function registrar() {
    $usuarioModel = $this->model('UsuarioModel');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recoger datos del formulario
        $nombre = trim($_POST['nombre']);
        $email = trim($_POST['email']);
        $email_recuperacion = trim($_POST['email_recuperacion']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $rol = $_POST['rol'];

        // Validaciones básicas
        if (empty($nombre) || empty($email) || empty($password) || empty($rol)) {
            $_SESSION['error'] = "Todos los campos obligatorios deben completarse.";
            $this->view('auth/registro');
            return;
        }

        if (!empty($email_recuperacion) && !filter_var($email_recuperacion, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "El correo de recuperación no es válido.";
            $this->view('auth/registro');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "El formato del email principal no es válido";
            $this->view('auth/registro');
            return;
        }

        if ($password !== $confirm_password) {
            $_SESSION['error'] = "Las contraseñas no coinciden";
            $this->view('auth/registro');
            return;
        }

        // Verificar si el email ya existe
        if ($usuarioModel->getUserByEmail($email)) {
            $_SESSION['error'] = "Este email ya está registrado";
            $this->view('auth/registro');
            return;
        }

        // Hash de la contraseña
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Crear usuario (activo = 0 por defecto)
        $resultado = $usuarioModel->crearUsuario([
            'nombre' => $nombre, // ya está completo
            'email' => $email,
            'email_recuperacion' => $email_recuperacion,
            'password_hash' => $hashed_password,
            'rol' => $rol,
            'activo' => 0
        ]);

        if ($resultado) {
            $_SESSION['success'] = "Registro exitoso. Su cuenta está pendiente de activación por un administrador.";
            $this->redirect('auth/login'); // Redirige a login después de registrar
        } else {
            $_SESSION['error'] = "Error al registrar el usuario. Intente nuevamente.";
            $this->view('auth/registro');
        }

    } else {
        // Mostrar formulario solo si es GET
        $this->view('auth/registro');
    }
}


public function forgotPassword() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email']);
        $usuarioModel = $this->model('UsuarioModel');
        $usuario = $usuarioModel->getUserByEmail($email);

        if ($usuario) {
            // Generar token válido por 2 minutos
            $token = bin2hex(random_bytes(16));
            $expira = date("Y-m-d H:i:s", strtotime("+2 minutes"));

            $usuarioModel->guardarTokenRecuperacion($usuario['id'], $token, $expira);

            // Aquí normalmente se enviaría un correo con el enlace de recuperación
            $_SESSION['success'] = "Se ha enviado un enlace de recuperación (simulado). 
            URL: " . BASE_URL . "auth/resetPassword?token=$token";
        } else {
            $_SESSION['error'] = "El email no está registrado.";
        }

        $this->redirect('auth/forgotPassword');
    } else {
        $this->view('auth/forgot_password');
    }
}

public function resetPassword() {
    if (isset($_GET['token'])) {
        $token = $_GET['token'];
        $usuarioModel = $this->model('UsuarioModel');
        $usuario = $usuarioModel->getUserByToken($token);

        if ($usuario) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $newPass = $_POST['password'];
                $confirm = $_POST['confirm_password'];

                if ($newPass === $confirm) {
                    $hashed = password_hash($newPass, PASSWORD_DEFAULT);
                    $usuarioModel->actualizarPassword($usuario['id'], $hashed);

                    $_SESSION['success'] = "Contraseña restablecida. Inicia sesión.";
                    $this->redirect('auth/login');
                } else {
                    $_SESSION['error'] = "Las contraseñas no coinciden.";
                }
            }
            $this->view('auth/reset_password', ['token' => $token]);
        } else {
            $_SESSION['error'] = "Token inválido o expirado.";
            $this->redirect('auth/forgotPassword');
        }
    }
}

}

?>