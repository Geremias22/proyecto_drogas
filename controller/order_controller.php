<?php
require_once 'controller/auth_controller.php';
require_once 'model/ProductModel.php';
require_once 'model/OrderModel.php';

class OrderController
{
    public function __construct()
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    // Muestra pantalla de checkout (requiere login)
    public function checkout()
    {
        AuthController::checkLogin();

        if (empty($_SESSION['cart'])) {
            header("Location: index.php?c=cart&a=index&error=Tu carrito está vacío");
            exit();
        }

        // Reutilizamos lógica estilo CartController para pintar resumen
        $productsInCart = [];
        $total = 0;
        $productModel = new ProductModel();

        foreach ($_SESSION['cart'] as $id => $quantity) {
            $product = $productModel->getById($id); // Asegúrate de implementarlo
            if ($product) {
                $qty = (int)$quantity;
                $product['quantity'] = $qty;
                $product['subtotal'] = $product['price'] * $qty;
                $total += $product['subtotal'];
                $productsInCart[] = $product;
            }
        }

        ob_start();
        require_once 'views/checkout.php'; // tendrás que crearlo
        $viewContent = ob_get_clean();
        require_once 'views/layouts/main.php';
    }

    // Confirma pedido (POST)
    public function place()
    {
        AuthController::checkLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=order&a=checkout");
            exit();
        }

        if (empty($_SESSION['cart'])) {
            header("Location: index.php?c=cart&a=index&error=Tu carrito está vacío");
            exit();
        }

        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if ($phone === '' || $address === '') {
            header("Location: index.php?c=order&a=checkout&error=Rellena teléfono y dirección");
            exit();
        }

        $orderModel = new OrderModel();

        // Crea pedido a partir del carrito (OrderModel lo implementaremos)
        try {
            $orderRef = $orderModel->createFromCart(
                (int)$_SESSION['user_id'],
                $phone,
                $address,
                $_SESSION['cart']
            );
        } catch (Exception $e) {
            header("Location: index.php?c=order&a=checkout&error=" . urlencode($e->getMessage()));
            exit();
        }

        // Vaciar carrito
        $_SESSION['cart'] = [];

        header("Location: index.php?c=home&a=index&msg=Pedido creado: " . urlencode($orderRef));
        exit();
    }
}
