<?php
require_once 'model/ProductModel.php';

class ProductShowController
{
    public function index()
    {
        // 1) Validar id
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            header("Location: index.php?c=product_list&a=index&error=Producto no válido");
            exit();
        }

        // 2) Buscar producto
        $model = new ProductModel();
        $product = $model->getById($id);

        if (!$product) {
            header("Location: index.php?c=product_list&a=index&error=Producto no encontrado");
            exit();
        }

        // 3) Render vista
        ob_start();
        require 'views/product_show.php';
        $viewContent = ob_get_clean();

        require 'views/layouts/main.php';
    }
}
