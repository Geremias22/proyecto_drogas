<?php
require_once 'model/UserModel.php';

class AuthController
{

    // --- MÉTODOS DE SEGURIDAD (ESTÁTICOS) ---

    public static function checkLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?c=auth&a=index&error=Debes iniciar sesión");
            exit();
        }
    }

    public static function checkAdmin()
    {
        self::checkLogin(); // Primero que esté logueado
        if ($_SESSION['user_role'] !== 'admin') {
            header("Location: index.php?c=home&a=index&error=No tienes permisos de administrador");
            exit();
        }
    }

    // --- MÉTODOS DE ACCIÓN ---

    public function index()
    {
        $mode = $_GET['mode'] ?? 'login';

        ob_start();
        require_once 'views/auth.php';
        $viewContent = ob_get_clean();
        require_once 'views/layouts/main.php';
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['gmail'] ?? '');
            $pass  = (string)($_POST['password'] ?? '');

            if ($email === '' || $pass === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                header("Location: index.php?c=auth&a=index&mode=login&error=" . urlencode("Credenciales inválidas"));
                exit();
            }

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

                    if ($valid) {
                        $newHash = password_hash($pass, PASSWORD_DEFAULT);
                        $userModel->updatePasswordHash((int)$user['id'], $newHash);
                    }
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

    public function logout()
    {
        session_destroy();
        header("Location: index.php?c=home&a=index&msg=Sesión cerrada correctamente");
    }

    public function register()
    {
        ob_start();
        require_once 'views/register.php';
        $viewContent = ob_get_clean();
        require_once 'views/layouts/main.php';
    }

    public function registerPost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=auth&a=index&mode=register");
            exit();
        }

        // 1) Normalización
        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['gmail'] ?? '');
        $pass  = (string)($_POST['password'] ?? '');

        $edadRaw = $_POST['edad'] ?? null;
        $edad = ($edadRaw === '' || $edadRaw === null) ? null : (int)$edadRaw;

        $adult = (int)($_POST['adult_confirm'] ?? 0);

        // 2) Obligatorios
        if ($name === '' || $email === '' || $pass === '') {
            header("Location: index.php?c=auth&a=index&mode=register&error=" . urlencode("Rellena los campos obligatorios"));
            exit();
        }

        // 3) Check + edad
        if ($adult !== 1) {
            header("Location: index.php?c=auth&a=index&mode=register&error=" . urlencode("Debes confirmar que eres mayor de 18 años"));
            exit();
        }

        if ($edad !== null) {
            if ($edad < 0 || $edad > 120) {
                header("Location: index.php?c=auth&a=index&mode=register&error=" . urlencode("Edad no válida"));
                exit();
            }
            // Si quieres endurecer (opcional):
            // if ($edad < 18) { ... }
        }

        // 4) Validación nombre (simple y realista)
        if (mb_strlen($name) < 3 || mb_strlen($name) > 60) {
            header("Location: index.php?c=auth&a=index&mode=register&error=" . urlencode("El nombre debe tener entre 3 y 60 caracteres"));
            exit();
        }

        // 5) Email formato
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: index.php?c=auth&a=index&mode=register&error=" . urlencode("Email no válido"));
            exit();
        }
        if (mb_strlen($email) > 255) {
            header("Location: index.php?c=auth&a=index&mode=register&error=" . urlencode("Email demasiado largo"));
            exit();
        }

        // 6) Password fuerte
        $pwdError = $this->validatePassword($pass);
        if ($pwdError !== null) {
            header("Location: index.php?c=auth&a=index&mode=register&error=" . urlencode($pwdError));
            exit();
        }

        // 7) Comprobar email duplicado
        $userModel = new UserModel();
        if ($userModel->emailExists($email)) {
            header("Location: index.php?c=auth&a=index&mode=register&error=" . urlencode("Ese email ya está registrado"));
            exit();
        }

        $question = trim($_POST['security_question'] ?? '');
        $answer   = trim($_POST['security_answer'] ?? '');

        if ($question === '' || $answer === '') {
            header("Location: index.php?c=auth&a=index&mode=register&error=" . urlencode("Selecciona una pregunta y escribe una respuesta"));
            exit();
        }

        // 8) Crear
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $newId = $userModel->create($name, $email, $hash, $edad);

        $answerHash = password_hash($answer, PASSWORD_DEFAULT);
        $userModel->setSecurityQA((int)$newId, $question, $answerHash);

        $_SESSION['user_id'] = $newId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_role'] = 'customer';

        header("Location: index.php?c=home&a=index&msg=" . urlencode("Cuenta creada correctamente"));
        exit();
    }

    // Helper dentro del AuthController
    private function validatePassword(string $pass): ?string
    {
        if (strlen($pass) < 8) return "La contraseña debe tener al menos 8 caracteres";
        if (strlen($pass) > 72) return "La contraseña es demasiado larga";
        if (!preg_match('/[A-Z]/', $pass)) return "La contraseña debe incluir al menos 1 mayúscula";
        if (!preg_match('/[a-z]/', $pass)) return "La contraseña debe incluir al menos 1 minúscula";
        if (!preg_match('/\d/', $pass)) return "La contraseña debe incluir al menos 1 número";
        if (!preg_match('/[^A-Za-z0-9]/', $pass)) return "La contraseña debe incluir al menos 1 símbolo";
        return null;
    }


    public function checkEmail()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
            exit();
        }

        $email = trim($_GET['email'] ?? '');

        if ($email === '') {
            echo json_encode(['ok' => true, 'valid' => false, 'available' => false, 'message' => 'Email requerido']);
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['ok' => true, 'valid' => false, 'available' => false, 'message' => 'Formato de email no válido']);
            exit();
        }

        $userModel = new UserModel();
        $exists = $userModel->emailExists($email);

        echo json_encode([
            'ok' => true,
            'valid' => true,
            'available' => !$exists,
            'message' => $exists ? 'Este email ya está en uso' : 'Email disponible'
        ]);
        exit();
    }

    public function forgot()
    {
        ob_start();
        require 'views/forgot.php';
        $viewContent = ob_get_clean();
        require 'views/layouts/main.php';
    }

    public function forgotPost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=auth&a=forgot");
            exit();
        }

        $email = trim($_POST['gmail'] ?? '');

        // Mensaje siempre "neutral" para no filtrar si existe
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: index.php?c=auth&a=forgot&msg=" . urlencode("Si el email existe, podrás continuar con la recuperación."));
            exit();
        }

        $userModel = new UserModel();
        $row = $userModel->getSecurityQuestionByEmail($email);

        if (!$row || empty($row['security_question'])) {
            header("Location: index.php?c=auth&a=forgot&msg=" . urlencode("Si el email existe, podrás continuar con la recuperación."));
            exit();
        }

        // Pasamos el ID para la siguiente pantalla
        header("Location: index.php?c=auth&a=reset&uid=" . (int)$row['id']);
        exit();
    }

    public function reset()
    {
        $uid = (int)($_GET['uid'] ?? 0);
        if ($uid <= 0) {
            header("Location: index.php?c=auth&a=forgot&error=" . urlencode("Solicitud inválida"));
            exit();
        }

        if (!isset($_SESSION['reset_attempts'])) {
            $_SESSION['reset_attempts'] = [];
        }
        if (!isset($_SESSION['reset_attempts'][$uid])) {
            $_SESSION['reset_attempts'][$uid] = 0;
        }
        $userModel = new UserModel();
        $user = $userModel->getSecurityById($uid);

        if (!$user || empty($user['security_question'])) {
            header("Location: index.php?c=auth&a=forgot&error=" . urlencode("No se puede recuperar la contraseña para este usuario"));
            exit();
        }

        $question = $user['security_question'];

        ob_start();
        require 'views/reset.php';
        $viewContent = ob_get_clean();
        require 'views/layouts/main.php';
    }

    public function resetPost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=auth&a=forgot");
            exit();
        }

        $uid = (int)($_POST['uid'] ?? 0);
        $answer = trim($_POST['security_answer'] ?? '');
        $newPass = (string)($_POST['new_password'] ?? '');

        
        if ($uid <= 0 || $answer === '' || $newPass === '') {
            header("Location: index.php?c=auth&a=reset&uid=$uid&error=" . urlencode("Rellena todos los campos"));
            exit();
        }

        // Validación fuerte de la nueva contraseña
        $pwdError = $this->validatePassword($newPass);
        if ($pwdError !== null) {
            header("Location: index.php?c=auth&a=reset&uid=$uid&error=" . urlencode($pwdError));
            exit();
        }

        $userModel = new UserModel();
        $user = $userModel->getSecurityById($uid);

        if (!$user || empty($user['security_answer_hash'])) {
            header("Location: index.php?c=auth&a=forgot&error=" . urlencode("No se puede recuperar la contraseña para este usuario"));
            exit();
        }

        if (!password_verify($answer, $user['security_answer_hash'])) {
            header("Location: index.php?c=auth&a=reset&uid=$uid&error=" . urlencode("Respuesta incorrecta"));
            exit();
        }

        $hash = password_hash($newPass, PASSWORD_DEFAULT);
        $userModel->updatePasswordHash($uid, $hash);

        header("Location: index.php?c=auth&a=index&mode=login&msg=" . urlencode("Contraseña actualizada. Ya puedes iniciar sesión."));
        exit();
    }
}
