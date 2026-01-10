<?php
require_once 'controller/auth_controller.php';
require_once 'model/StockModel.php';

class StockController
{
    public function index()
    {
        AuthController::checkAdmin();

        $q = $_GET['q'] ?? '';

        $stockModel = new StockModel();
        $rows = $stockModel->getAllWithProduct($q);

        ob_start();
        require 'views/admin/stock/index.php';
        $viewContent = ob_get_clean();
        require 'views/layouts/main.php';
    }

    // Ajuste absoluto
    public function set()
    {
        AuthController::checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=stock&a=index");
            exit();
        }

        $pid = (int)($_POST['product_id'] ?? 0);
        $qty = (int)($_POST['qty'] ?? -1);

        if ($pid <= 0) {
            header("Location: index.php?c=stock&a=index&error=Producto inválido");
            exit();
        }

        try {
            $stockModel = new StockModel();
            $stockModel->setQuantity($pid, $qty);
            header("Location: index.php?c=stock&a=index&msg=Stock actualizado");
        } catch (Exception $e) {
            header("Location: index.php?c=stock&a=index&error=" . urlencode($e->getMessage()));
        }
        exit();
    }

    // Ajuste relativo (IN / OUT)
    public function move()
    {
        AuthController::checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=stock&a=index");
            exit();
        }

        $pid = (int)($_POST['product_id'] ?? 0);
        $qty = (int)($_POST['qty'] ?? 0);
        $type = $_POST['type'] ?? '';

        if ($pid <= 0 || $qty <= 0) {
            header("Location: index.php?c=stock&a=index&error=Datos inválidos");
            exit();
        }

        try {
            $stockModel = new StockModel();
            $stockModel->changeQuantity($pid, $qty, $type);
            header("Location: index.php?c=stock&a=index&msg=Movimiento registrado");
        } catch (Exception $e) {
            header("Location: index.php?c=stock&a=index&error=" . urlencode($e->getMessage()));
        }
        exit();
    }

    public function history()
    {
        AuthController::checkAdmin();

        $pid = (int)($_GET['product_id'] ?? 0);
        if ($pid <= 0) {
            header("Location: index.php?c=stock&a=index&error=Producto inválido");
            exit();
        }

        $stockModel = new StockModel();
        $stock = $stockModel->getByProductId($pid);
        if (!$stock) {
            header("Location: index.php?c=stock&a=index&error=Stock no encontrado");
            exit();
        }

        $movements = $stockModel->getMovementsByProduct($pid, 100);

        ob_start();
        require 'views/admin/stock/history.php';
        $viewContent = ob_get_clean();
        require 'views/layouts/main.php';
    }
}
