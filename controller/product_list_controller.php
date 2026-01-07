<?php
require_once 'model/ProductModel.php';

class ProductListController {
    
    public function index() {
        $model = new ProductModel();
        $products = $model->getAllActive();

        // Empezamos a capturar el contenido de la vista
        ob_start();
        require_once 'views/product_list.php';
        $viewContent = ob_get_clean();

        // Lo mandamos al layout principal
        require_once 'views/layouts/main.php';
    }
}