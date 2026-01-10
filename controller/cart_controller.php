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

    // Añade un producto al carrito (con validación backend)
    public function add() {
        if (!isset($_GET['id'])) {
            header("Location: index.php?c=product_list&a=index&error=Producto no válido");
            exit();
        }

        $id = (int)$_GET['id'];
        $qty = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

        if ($id <= 0 || $qty <= 0) {
            header("Location: index.php?c=product_list&a=index&error=Datos inválidos");
            exit();
        }

        $model = new ProductModel();
        $product = $model->getById($id);

        if (!$product) {
            header("Location: index.php?c=product_list&a=index&error=Producto no encontrado");
            exit();
        }

        // Si tu tabla tiene is_active y el modelo lo trae, validamos
        if (isset($product['is_active']) && (int)$product['is_active'] !== 1) {
            header("Location: index.php?c=product_list&a=index&error=Producto no disponible");
            exit();
        }

        $stock = (int)($product['stock_qty'] ?? 0);

        // Cantidad final en carrito (si ya había)
        $current = (int)($_SESSION['cart'][$id] ?? 0);
        $newTotal = $current + $qty;

        if ($stock <= 0) {
            header("Location: index.php?c=product_show&a=index&id=$id&error=Producto sin stock");
            exit();
        }

        if ($newTotal > $stock) {
            header("Location: index.php?c=product_show&a=index&id=$id&error=Stock insuficiente. Disponible: $stock");
            exit();
        }

        // OK: actualizamos carrito
        $_SESSION['cart'][$id] = $newTotal;

        // Mensaje opcional
        $_SESSION['flash_msg'] = "Producto añadido";

        // Volver a donde estaba el usuario
        $back = $_SERVER['HTTP_REFERER'] ?? 'index.php?c=product_list&a=index';
        header("Location: " . $back);
        
        exit();
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