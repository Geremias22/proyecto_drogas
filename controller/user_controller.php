<?php
require_once 'controller/auth_controller.php';
require_once 'model/UserModel.php';
require_once 'model/OrderModel.php';
require_once 'helpers/SecurityHelper.php';

class UserController
{
    // Listado de usuarios (solo admin)
    public function index()
    {
        AuthController::checkAdmin();

        $userModel = new UserModel();
        $users = $userModel->getAll(); // implementar en UserModel

        ob_start();
        require_once 'views/admin/users_list.php'; // tendrás que crearlo
        $viewContent = ob_get_clean();
        require_once 'views/layouts/main.php';
    }

    // Cambiar rol (POST) (solo admin)
    public function setRole()
    {
        AuthController::checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=user&a=index");
            exit();
        }

        // Validar CSRF token
        $csrf_token = trim($_POST['csrf_token'] ?? '');
        if (!SecurityHelper::validateCsrfToken($csrf_token)) {
            header("Location: index.php?c=user&a=index&error=" . urlencode("Solicitud inválida (CSRF)"));
            exit();
        }

        $id = (int)($_POST['id'] ?? 0);
        $role = $_POST['role'] ?? 'customer';

        $userModel = new UserModel();
        $ok = $userModel->updateRole($id, $role); // implementar

        if ($ok) {
            header("Location: index.php?c=user&a=index&msg=Rol actualizado");
        } else {
            header("Location: index.php?c=user&a=index&error=No se pudo actualizar el rol");
        }
        exit();
    }

    // Activar/desactivar usuario (POST) (solo admin)
    public function toggleActive()
    {
        AuthController::checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=user&a=index");
            exit();
        }

        // Validar CSRF token
        $csrf_token = trim($_POST['csrf_token'] ?? '');
        if (!SecurityHelper::validateCsrfToken($csrf_token)) {
            header("Location: index.php?c=user&a=index&error=" . urlencode("Solicitud inválida (CSRF)"));
            exit();
        }

        $id = (int)($_POST['id'] ?? 0);
        $active = (int)($_POST['active'] ?? 1);

        $userModel = new UserModel();
        $ok = $userModel->setActive($id, $active); // implementar

        if ($ok) {
            header("Location: index.php?c=user&a=index&msg=Estado actualizado");
        } else {
            header("Location: index.php?c=user&a=index&error=No se pudo actualizar el estado");
        }
        exit();
    }

    public function profile()
    {
        AuthController::checkLogin();

        $userModel = new UserModel();
        $orderModel = new OrderModel();

        $user = $userModel->getById((int)$_SESSION['user_id']);   // lo añadimos abajo
        $orders = $orderModel->getOrdersByUser((int)$_SESSION['user_id']);

        ob_start();
        require 'views/user_profile.php';
        $viewContent = ob_get_clean();
        require 'views/layouts/main.php';
    }
}
