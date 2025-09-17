<?php
require_once 'models/UsuarioModel.php';
require_once 'vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class RecuperarController {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
    }

    // Mostrar formulario de "olvidé mi contraseña"
    public function forgotPasswordForm() {
        include 'views/auth/forgot_password.php';
    }

    // Procesar envío de correo
    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';

            $usuario = $this->usuarioModel->getUserByEmail($email);

            if ($usuario) {
                $token = bin2hex(random_bytes(32));
                $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

                $this->usuarioModel->guardarTokenRecuperacion($usuario['id'], $token, $expira);

                $this->enviarCorreo($usuario['email'], $usuario['nombre'], $token);

                $_SESSION['success'] = "Hemos enviado un enlace a tu correo.";
                header("Location: " . BASE_URL . "auth/login");
                exit;
            } else {
                $_SESSION['error'] = "El correo no está registrado.";
                header("Location: " . BASE_URL . "auth/forgotPasswordForm");
                exit;
            }
        }
    }

    // Mostrar formulario para restablecer contraseña
    public function resetPasswordForm($token) {
        $usuario = $this->usuarioModel->getUserByToken($token);

        if ($usuario) {
            $token = htmlspecialchars($token);
            include 'views/auth/reset_password.php';
        } else {
            $_SESSION['error'] = "El enlace no es válido o ha expirado.";
            header("Location: " . BASE_URL . "auth/forgotPasswordForm");
            exit;
        }
    }

    // Procesar nueva contraseña
    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            if ($password !== $confirm) {
                $_SESSION['error'] = "Las contraseñas no coinciden.";
                header("Location: " . BASE_URL . "auth/resetPasswordForm&token=" . urlencode($token));
                exit;
            }

            $usuario = $this->usuarioModel->getUserByToken($token);

            if ($usuario) {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $this->usuarioModel->actualizarPassword($usuario['id'], $hash);

                $_SESSION['success'] = "Tu contraseña fue actualizada correctamente.";
                header("Location: " . BASE_URL . "auth/login");
                exit;
            } else {
                $_SESSION['error'] = "Token inválido.";
                header("Location: " . BASE_URL . "auth/forgotPasswordForm");
                exit;
            }
        }
    }

    // Función privada para enviar correo
    private function enviarCorreo($email, $nombre, $token) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'tu_correo@gmail.com';   // 👈 tu correo
            $mail->Password = 'APP_PASSWORD';         // 👈 contraseña de aplicación
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('tu_correo@gmail.com', 'Cafetería Nova');
            $mail->addAddress($email, $nombre);

            $mail->isHTML(true);
            $mail->Subject = "Recuperación de contraseña";
            $mail->Body    = "Hola $nombre, <br><br>
                Haz clic en este enlace para restablecer tu contraseña: <br>
                <a href='" . BASE_URL . "auth/resetPasswordForm&token=$token'>
                Recuperar Contraseña</a><br><br>
                Este enlace expira en 1 hora.";

            $mail->send();
        } catch (Exception $e) {
            error_log("Error enviando correo: {$mail->ErrorInfo}");
        }
    }
}
