<?php
require_once 'model/PackModel.php';
require_once 'model/PackProductModel.php';

class PackController
{
    public function show()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            header("Location: index.php?c=home&a=index&error=Pack no válido");
            exit();
        }

        $packModel = new PackModel();
        $pack = $packModel->getById($id);

        if (!$pack) {
            header("Location: index.php?c=home&a=index&error=Pack no encontrado");
            exit();
        }

        $ppModel = new PackProductModel();
        $items = $ppModel->getProductsByPackId($id);

        // 1) SUMA (precio * qty)
        $sum = 0.0;
        foreach ($items as $it) {
            $sum += ((float)$it['price']) * ((int)$it['qty']);
        }

        // 2) FINAL según regla del pack (precio fijo o descuento)
        $calc = $this->calcPackFinal($sum, $pack);
        $final = (float)$calc['final'];
        $mode  = (string)$calc['mode'];

        // (Opcional) Por compatibilidad con tu vista antigua
        $total = $final;

        ob_start();
        require 'views/pack_show.php';
        $viewContent = ob_get_clean();

        require 'views/layouts/main.php';
    }

    // Misma lógica que en admin, pero privada aquí
    private function calcPackFinal(float $sum, array $pack): array
    {
        $finalPrice = $pack['final_price'] ?? null;
        $disc = $pack['discount_percent'] ?? null;

        $final = $sum;
        $mode = 'suma';

        if ($finalPrice !== null && $finalPrice !== '') {
            $final = (float)$finalPrice;
            $mode = 'precio_fijo';
        } elseif ($disc !== null && $disc !== '') {
            $d = (float)$disc;
            if ($d < 0) $d = 0;
            if ($d > 100) $d = 100;
            $final = $sum * (1 - ($d / 100));
            $mode = 'descuento';
        }

        return [
            'sum' => $sum,
            'final' => $final,
            'mode' => $mode,
        ];
    }
}
