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
        $mode = $_GET['mode'] ?? 'login';

        ob_start();
        require_once 'views/auth.php';
        $viewContent = ob_get_clean();
        require_once 'views/layouts/main.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['gmail'];
            $pass = $_POST['password'];

            $userModel = new UserModel();
            $user = $userModel->getUserByEmail($email);

            $valid = false;

            if ($user) {
                $stored = $user['password'] ?? '';

                if ($stored !== '' && $stored[0] === '$') {
                    // Hash moderno
                    $valid = password_verify($pass, $stored);
                } else {
                    // Password en claro (compatibilidad)
                    $valid = ($stored === $pass);
                }
            }

            if ($valid) {
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

    public function register() {
    ob_start();
        require_once 'views/register.php';
        $viewContent = ob_get_clean();
        require_once 'views/layouts/main.php';
    }

    public function registerPost() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=auth&a=register");
            exit();
        }

        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['gmail'] ?? '');
        $pass  = $_POST['password'] ?? '';
        $edad  = $_POST['edad'] ?? null;

        if ($name === '' || $email === '' || $pass === '') {
            header("Location: index.php?c=auth&a=register&error=Rellena los campos obligatorios");
            exit();
        }

        $userModel = new UserModel();

        // Necesitas añadir este método al UserModel
        if ($userModel->emailExists($email)) {
            header("Location: index.php?c=auth&a=register&error=Ese email ya está registrado");
            exit();
        }

        $hash = password_hash($pass, PASSWORD_DEFAULT);

        // Necesitas añadir este método al UserModel
        $newId = $userModel->create($name, $email, $hash, $edad);

        // Auto-login
        $_SESSION['user_id'] = $newId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_role'] = 'customer';

        header("Location: index.php?c=home&a=index&msg=Cuenta creada correctamente");
    }

}