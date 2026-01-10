<?php
require_once 'model/ProductModel.php';

class ProductListController {
    
    public function index() {
        $model = new ProductModel();

        $q = $_GET['q'] ?? '';
        $categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;

        // Si hay filtro o búsqueda, usamos searchActive
        if (($categoryId !== null && $categoryId > 0) || trim($q) !== '') {
            $products = $model->searchActive($q, $categoryId);
        } else {
            $products = $model->getAllActive();
        }

        // Para que la vista sepa en qué categoría estamos
        $currentCategory = null;
        if ($categoryId) {
            require_once 'model/CategoryModel.php';
            $catModel = new CategoryModel();
            $currentCategory = $catModel->getById($categoryId);
        }
        ob_start();
        require 'views/product_list.php';
        $viewContent = ob_get_clean();

        require 'views/layouts/main.php';
    }


    public function ajax() {
        $model = new ProductModel();

        $q = $_GET['q'] ?? '';
        $categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;

        $products = $model->searchActive($q, $categoryId);

        // Devolvemos SOLO el HTML de la grilla, no el layout
        require 'views/partials/product_grid.php';
    }

}