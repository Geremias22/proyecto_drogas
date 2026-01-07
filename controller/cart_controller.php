<?php
require_once 'model/ProductModel.php';

class CartController {

    public function __construct() {
        // Inicializamos el carrito si no existe
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    // Muestra el carrito
    public function index() {
        $productsInCart = [];
        $total = 0;
        $model = new ProductModel();

        // Recorremos la sesión para buscar los detalles de cada producto
        foreach ($_SESSION['cart'] as $id => $quantity) {
            $product = $model->getById($id);
            if ($product) {
                $product['quantity'] = $quantity;
                $product['subtotal'] = $product['price'] * $quantity;
                $total += $product['subtotal'];
                $productsInCart[] = $product;
            }
        }

        ob_start();
        require_once 'views/cart.php';
        $viewContent = ob_get_clean();
        require_once 'views/layouts/main.php';
    }

    // Añade un producto al carrito
    public function add() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $qty = $_POST['quantity'] ?? 1;

            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id] += $qty;
            } else {
                $_SESSION['cart'][$id] = $qty;
            }
            header("Location: index.php?c=cart&a=index&msg=Producto añadido");
        }
    }

    // Elimina un producto
    public function remove() {
        if (isset($_GET['id'])) {
            unset($_SESSION['cart'][$_GET['id']]);
        }
        header("Location: index.php?c=cart&a=index");
    }

    // Vacía todo el carrito
    public function clear() {
        $_SESSION['cart'] = [];
        header("Location: index.php?c=cart&a=index");
    }
}