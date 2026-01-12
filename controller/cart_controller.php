<?php
require_once 'model/ProductModel.php';

class CartController
{

    public function __construct()
    {
        // Inicializamos el carrito si no existe
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    // Muestra el carrito
    public function index()
    {
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

    // Dentro de CartController

    private function isAjax(): bool
    {
        return (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        );
    }

    private function jsonResponse(array $payload, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit();
    }

    private function cartCount(): int
    {
        // total de unidades (no de líneas)
        return array_sum($_SESSION['cart'] ?? []);
    }

    public function add()
    {
        $isAjax = $this->isAjax();

        if (!isset($_GET['id'])) {
            if ($isAjax) $this->jsonResponse(['ok' => false, 'message' => 'Producto no válido'], 400);
            header("Location: index.php?c=product_list&a=index&error=Producto no válido");
            exit();
        }

        $id = (int)$_GET['id'];
        $qty = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

        if ($id <= 0 || $qty <= 0) {
            if ($isAjax) $this->jsonResponse(['ok' => false, 'message' => 'Datos inválidos'], 400);
            header("Location: index.php?c=product_list&a=index&error=Datos inválidos");
            exit();
        }

        $model = new ProductModel();
        $product = $model->getById($id);

        if (!$product) {
            if ($isAjax) $this->jsonResponse(['ok' => false, 'message' => 'Producto no encontrado'], 404);
            header("Location: index.php?c=product_list&a=index&error=Producto no encontrado");
            exit();
        }

        if (isset($product['is_active']) && (int)$product['is_active'] !== 1) {
            if ($isAjax) $this->jsonResponse(['ok' => false, 'message' => 'Producto no disponible'], 409);
            header("Location: index.php?c=product_list&a=index&error=Producto no disponible");
            exit();
        }

        $stock = (int)($product['stock_qty'] ?? 0);
        $current = (int)($_SESSION['cart'][$id] ?? 0);
        $newTotal = $current + $qty;

        if ($stock <= 0) {
            if ($isAjax) $this->jsonResponse(['ok' => false, 'message' => 'Producto sin stock'], 409);
            header("Location: index.php?c=product_show&a=index&id=$id&error=Producto sin stock");
            exit();
        }

        if ($newTotal > $stock) {
            $msg = "Stock insuficiente. Disponible: $stock";
            if ($isAjax) $this->jsonResponse(['ok' => false, 'message' => $msg, 'available' => $stock], 409);
            header("Location: index.php?c=product_show&a=index&id=$id&error=" . urlencode($msg));
            exit();
        }

        // OK
        $_SESSION['cart'][$id] = $newTotal;
        $_SESSION['flash_msg'] = "Producto añadido";

        if ($isAjax) {
            $this->jsonResponse([
                'ok' => true,
                'message' => 'Producto añadido',
                'product_id' => $id,
                'line_qty' => $newTotal,        // qty de esa línea en el carrito
                'cart_count' => $this->cartCount(), // total unidades
            ]);
        }

        $back = $_SERVER['HTTP_REFERER'] ?? 'index.php?c=product_list&a=index';
        header("Location: " . $back);
        exit();
    }



    // Elimina un producto
    public function remove()
    {
        if (isset($_GET['id'])) {
            unset($_SESSION['cart'][$_GET['id']]);
        }
        header("Location: index.php?c=cart&a=index");
    }

    // Vacía todo el carrito
    public function clear()
    {
        $_SESSION['cart'] = [];
        header("Location: index.php?c=cart&a=index");
    }
}
