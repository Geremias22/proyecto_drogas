<?php
require_once 'model/UserModel.php';

class AuthController {

    // --- MÉTODOS DE SEGURIDAD (ESTÁTICOS) ---
    
    public static function checkLogin() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?c=auth&a=index&error=Debes iniciar sesión");
            exit();
        }
    }

    public static function checkAdmin() {
        self::checkLogin(); // Primero que esté logueado
        if ($_SESSION['user_role'] !== 'admin') {
            header("Location: index.php?c=home&a=index&error=No tienes permisos de administrador");
            exit();
        }
    }

    // --- MÉTODOS DE ACCIÓN ---

    public function index() {
        ob_start();
        require_once 'views/login.php';
        $viewContent = ob_get_clean();
        require_once 'views/layouts/main.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['gmail'];
            $pass = $_POST['password'];

            $userModel = new UserModel();
            $user = $userModel->getUserByEmail($email);

            if ($user && $user['password'] === $pass) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header("Location: index.php?c=home&a=index&msg=Bienvenido Administrador");
                } else {
                    header("Location: index.php?c=home&a=index&msg=Bienvenido a la asociación");
                }
            } else {
                header("Location: index.php?c=auth&a=index&error=Email o contraseña incorrectos");
            }
        }
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?c=home&a=index&msg=Sesión cerrada correctamente");
    }
}