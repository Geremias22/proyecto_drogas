<?php
require_once 'model/CategoryModel.php';
require_once 'model/PackModel.php';

class HomeController {

    public function index() {
        $name = $_SESSION['user_name'] ?? 'Invitado';

        // ✅ Si es admin: cargar home de admin
        if (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {

            ob_start();
            require 'views/admin/home.php';  // <-- nueva vista
            $viewContent = ob_get_clean();
            require 'views/layouts/main.php';
            return;
        }

        // ✅ Home normal (cliente / invitado)
        $categoryModel = new CategoryModel();
        $packModel = new PackModel();

        $categories = $categoryModel->getMainCategories();
        $packs = $packModel->getFeatured(6); // si no existe, luego lo hacemos

        ob_start();
        require 'views/home.php';
        $viewContent = ob_get_clean();
        require 'views/layouts/main.php';
    }
}
