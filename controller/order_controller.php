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
        $total = 0.0; // base total (sin IVA)
        $productModel = new ProductModel();

        foreach ($_SESSION['cart'] as $id => $quantity) {
            $product = $productModel->getById($id);

            if ($product) {
                $qty = (int)$quantity;

                // Seguridad mínima
                if ($qty <= 0) { $qty = 1; }

                $product['quantity'] = $qty;
                $product['subtotal'] = ((float)$product['price']) * $qty;

                $total += (float)$product['subtotal'];
                $productsInCart[] = $product;
            }
        }

        // ----------------------------
        // ✅ IVA real desglosado por tipo
        // ----------------------------
        // Estructura: [rate => ['name' => '', 'base' => 0.0, 'tax' => 0.0]]
        $taxBreakdown = [];
        $baseTotal = $total;

        foreach ($productsInCart as $p) {
            $lineBase = (float)$p['subtotal'];

            // rate_iva viene de taxes.rate_iva (ej 21.00, 10.00, 4.00)
            $rate = (float)($p['tax_rate'] ?? 0);
            $name = $p['tax_name'] ?? ('IVA ' . rtrim(rtrim(number_format($rate, 2), '0'), '.') . '%');

            $lineTax = $lineBase * ($rate / 100);

            if (!isset($taxBreakdown[$rate])) {
                $taxBreakdown[$rate] = [
                    'name' => $name,
                    'base' => 0.0,
                    'tax'  => 0.0,
                ];
            }

            $taxBreakdown[$rate]['base'] += $lineBase;
            $taxBreakdown[$rate]['tax']  += $lineTax;
        }

        $taxTotal = 0.0;
        foreach ($taxBreakdown as $row) {
            $taxTotal += (float)$row['tax'];
        }

        $grandTotal = $baseTotal + $taxTotal;

        ksort($taxBreakdown);

        ob_start();
        require 'views/payment.php';
        $viewContent = ob_get_clean();

        require 'views/layouts/main.php';
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

        $paymentMethod = trim($_POST['payment_method'] ?? '');
        if ($paymentMethod === '') {
            header("Location: index.php?c=order&a=checkout&error=Selecciona método de pago");
            exit();
        }

        $orderModel = new OrderModel();

        // Crea pedido a partir del carrito (OrderModel lo implementaremos)
        try {
            $orderRef = $orderModel->createFromCart(
                (int)$_SESSION['user_id'],
                $phone,
                $address,
                $paymentMethod,
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

    public function my()
    {
        AuthController::checkLogin();

        $orderModel = new OrderModel();
        $orders = $orderModel->getOrdersByUser((int)$_SESSION['user_id']);

        ob_start();
        require 'views/order_my.php';
        $viewContent = ob_get_clean();
        require 'views/layouts/main.php';
    }

    public function show()
    {
        AuthController::checkLogin();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            header("Location: index.php?c=user&a=profile&error=Pedido no válido");
            exit();
        }

        $orderModel = new OrderModel();
        $order = $orderModel->getOrderById($id, (int)$_SESSION['user_id']);

        if (!$order) {
            header("Location: index.php?c=user&a=profile&error=Pedido no encontrado");
            exit();
        }

        $items = $orderModel->getOrderItems($id);

        // Totales + desglose IVA
        $baseTotal = 0.0;
        $taxBreakdown = [];

        foreach ($items as $it) {
            $qty = (int)$it['quantity'];
            $price = (float)$it['price'];
            $lineBase = $qty * $price;

            $rate = (float)($it['tax_rate'] ?? 0);
            $name = $it['tax_name'] ?? ('IVA ' . $rate . '%');

            $lineTax = $lineBase * ($rate / 100);

            $baseTotal += $lineBase;

            if (!isset($taxBreakdown[$rate])) {
                $taxBreakdown[$rate] = ['name' => $name, 'base' => 0.0, 'tax' => 0.0];
            }
            $taxBreakdown[$rate]['base'] += $lineBase;
            $taxBreakdown[$rate]['tax']  += $lineTax;
        }

        ksort($taxBreakdown);

        $taxTotal = 0.0;
        foreach ($taxBreakdown as $row) $taxTotal += $row['tax'];

        $grandTotal = $baseTotal + $taxTotal;

        ob_start();
        require 'views/order_show.php';
        $viewContent = ob_get_clean();
        require 'views/layouts/main.php';
    }


}
