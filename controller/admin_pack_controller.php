<?php
require_once 'controller/auth_controller.php';
require_once 'model/PackModel.php';
require_once 'model/PackProductModel.php';
require_once 'model/ProductModel.php';
require_once 'helpers/SecurityHelper.php';

class AdminPackController
{
    public function index()
    {
        AuthController::checkAdmin();

        $packModel = new PackModel();
        $packs = $packModel->getAllAdmin();

        ob_start();
        require 'views/admin/packs/index.php';
        $viewContent = ob_get_clean();
        require 'views/layouts/main.php';
    }

    public function create()
    {
        AuthController::checkAdmin();

        $packModel = new PackModel();
        $parents = $packModel->getAllAdmin(); // por si quieres packs anidados

        $pack = null;
        $mode = 'create';

        ob_start();
        require 'views/admin/packs/form.php';
        $viewContent = ob_get_clean();
        require 'views/layouts/main.php';
    }

    public function store()
    {
        AuthController::checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=admin_pack&a=index");
            exit();
        }

        // Validar CSRF token
        $csrf_token = trim($_POST['csrf_token'] ?? '');
        if (!SecurityHelper::validateCsrfToken($csrf_token)) {
            header("Location: index.php?c=admin_pack&a=create&error=" . urlencode("Solicitud inválida (CSRF)"));
            exit();
        }

        $data = $this->readPackPost();
        
        // Validación server-side
        $error = $this->validatePack($data);
        if ($error) {
            header("Location: index.php?c=admin_pack&a=create&error=" . urlencode($error));
            exit();
        }

        $packModel = new PackModel();
        $ok = $packModel->createAdmin($data);

        if ($ok) {
            header("Location: index.php?c=admin_pack&a=index&msg=Pack creado");
        } else {
            header("Location: index.php?c=admin_pack&a=create&error=No se pudo crear el pack");
        }
        exit();
    }

    public function edit()
    {
        AuthController::checkAdmin();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header("Location: index.php?c=admin_pack&a=index&error=ID inválido");
            exit();
        }

        $packModel = new PackModel();
        $pack = $packModel->getById($id);

        if (!$pack) {
            header("Location: index.php?c=admin_pack&a=index&error=Pack no encontrado");
            exit();
        }

        $parents = array_filter($packModel->getAllAdmin(), fn($p) => (int)$p['id'] !== $id);
        $mode = 'edit';

        ob_start();
        require 'views/admin/packs/form.php';
        $viewContent = ob_get_clean();
        require 'views/layouts/main.php';
    }

    public function update()
    {
        AuthController::checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=admin_pack&a=index");
            exit();
        }

        // Validar CSRF token
        $csrf_token = trim($_POST['csrf_token'] ?? '');
        if (!SecurityHelper::validateCsrfToken($csrf_token)) {
            header("Location: index.php?c=admin_pack&a=index&error=" . urlencode("Solicitud inválida (CSRF)"));
            exit();
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            header("Location: index.php?c=admin_pack&a=index&error=ID inválido");
            exit();
        }

        $data = $this->readPackPost();
        
        // Validación server-side
        $error = $this->validatePack($data);
        if ($error) {
            header("Location: index.php?c=admin_pack&a=edit&id=$id&error=" . urlencode($error));
            exit();
        }

        $packModel = new PackModel();
        $ok = $packModel->updateAdmin($id, $data);

        if ($ok) {
            header("Location: index.php?c=admin_pack&a=index&msg=Pack actualizado");
        } else {
            header("Location: index.php?c=admin_pack&a=edit&id=$id&error=No se pudo actualizar");
        }
        exit();
    }

    public function delete()
    {
        AuthController::checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=admin_pack&a=index");
            exit();
        }

        // Validar CSRF token
        $csrf_token = trim($_POST['csrf_token'] ?? '');
        if (!SecurityHelper::validateCsrfToken($csrf_token)) {
            header("Location: index.php?c=admin_pack&a=index&error=" . urlencode("Solicitud inválida (CSRF)"));
            exit();
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            header("Location: index.php?c=admin_pack&a=index&error=ID inválido");
            exit();
        }

        $packModel = new PackModel();
        $ok = $packModel->deleteAdmin($id);

        if ($ok) {
            header("Location: index.php?c=admin_pack&a=index&msg=Pack eliminado");
        } else {
            header("Location: index.php?c=admin_pack&a=index&error=No se pudo eliminar");
        }
        exit();
    }

    // -----------------------------
    // Editar contenido del pack
    // -----------------------------

    public function items()
    {
        AuthController::checkAdmin();

        $packId = (int)($_GET['id'] ?? 0);
        if ($packId <= 0) {
            header("Location: index.php?c=admin_pack&a=index&error=ID inválido");
            exit();
        }

        $packModel = new PackModel();
        $ppModel   = new PackProductModel();
        $productModel = new ProductModel();

        $pack = $packModel->getById($packId);
        if (!$pack) {
            header("Location: index.php?c=admin_pack&a=index&error=Pack no encontrado");
            exit();
        }

        $items = $ppModel->getItems($packId);

        // Productos disponibles para añadir (activos)
        $q = trim($_GET['q'] ?? '');
        $products = $productModel->searchActive($q, null);

        // Cálculos
        $sum = 0.0;
        foreach ($items as $it) {
            $sum += ((float)$it['price']) * ((int)$it['qty']);
        }

        $final = $this->calcPackFinal($sum, $pack);

        ob_start();
        require 'views/admin/packs/items.php';
        $viewContent = ob_get_clean();
        require 'views/layouts/main.php';
    }

    public function itemAdd()
    {
        AuthController::checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=admin_pack&a=index");
            exit();
        }

        // Validar CSRF token
        $csrf_token = trim($_POST['csrf_token'] ?? '');
        if (!SecurityHelper::validateCsrfToken($csrf_token)) {
            header("Location: index.php?c=admin_pack&a=index&error=" . urlencode("Solicitud inválida (CSRF)"));
            exit();
        }

        $packId = (int)($_POST['pack_id'] ?? 0);
        $productId = (int)($_POST['product_id'] ?? 0);
        $qty = (int)($_POST['qty'] ?? 1);

        if ($packId <= 0 || $productId <= 0 || $qty <= 0) {
            header("Location: index.php?c=admin_pack&a=items&id=$packId&error=Datos inválidos");
            exit();
        }

        // Validar producto existe y activo
        $productModel = new ProductModel();
        $p = $productModel->getById($productId);
        if (!$p || (int)($p['is_active'] ?? 1) !== 1) {
            header("Location: index.php?c=admin_pack&a=items&id=$packId&error=Producto no válido");
            exit();
        }

        $ppModel = new PackProductModel();
        $ok = $ppModel->upsertItem($packId, $productId, $qty);

        if ($ok) {
            header("Location: index.php?c=admin_pack&a=items&id=$packId&msg=Producto añadido al pack");
        } else {
            header("Location: index.php?c=admin_pack&a=items&id=$packId&error=No se pudo añadir");
        }
        exit();
    }

    public function itemRemove()
    {
        AuthController::checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=admin_pack&a=index");
            exit();
        }

        // Validar CSRF token
        $csrf_token = trim($_POST['csrf_token'] ?? '');
        if (!SecurityHelper::validateCsrfToken($csrf_token)) {
            header("Location: index.php?c=admin_pack&a=index&error=" . urlencode("Solicitud inválida (CSRF)"));
            exit();
        }

        $packId = (int)($_POST['pack_id'] ?? 0);
        $productId = (int)($_POST['product_id'] ?? 0);

        if ($packId <= 0 || $productId <= 0) {
            header("Location: index.php?c=admin_pack&a=index&error=Datos inválidos");
            exit();
        }

        $ppModel = new PackProductModel();
        $ok = $ppModel->removeItem($packId, $productId);

        if ($ok) {
            header("Location: index.php?c=admin_pack&a=items&id=$packId&msg=Producto eliminado del pack");
        } else {
            header("Location: index.php?c=admin_pack&a=items&id=$packId&error=No se pudo eliminar");
        }
        exit();
    }

    public function priceInfo()
    {
        AuthController::checkAdmin();

        header('Content-Type: application/json; charset=utf-8');

        $packId = (int)($_GET['id'] ?? 0);
        if ($packId <= 0) {
            echo json_encode(['ok' => false, 'error' => 'ID inválido']);
            exit();
        }

        $packModel = new PackModel();
        $ppModel = new PackProductModel();

        $pack = $packModel->getById($packId);
        if (!$pack) {
            echo json_encode(['ok' => false, 'error' => 'Pack no encontrado']);
            exit();
        }

        $items = $ppModel->getItems($packId);

        $sum = 0.0;
        foreach ($items as $it) {
            $sum += ((float)$it['price']) * ((int)$it['qty']);
        }

        echo json_encode([
            'ok' => true,
            'sum' => $sum,
            'final_price' => $pack['final_price'],
            'discount_percent' => $pack['discount_percent'],
        ]);
        exit();
    }


    // -----------------------------
    // Helpers
    // -----------------------------

    private function readPackPost(): array
    {
        $name = trim($_POST['name'] ?? '');
        $image = trim($_POST['image'] ?? '');

        $parentId = ($_POST['parent_id'] ?? '') === '' ? null : (int)$_POST['parent_id'];

        $finalPrice = ($_POST['final_price'] ?? '') === '' ? null : (float)$_POST['final_price'];
        $discount = ($_POST['discount_percent'] ?? '') === '' ? null : (float)$_POST['discount_percent'];

        // Regla: si final_price viene, ignoramos discount (opcional)
        if ($finalPrice !== null) {
            $discount = null;
        }

        // Normalización
        if ($discount !== null) {
            if ($discount < 0) $discount = 0;
            if ($discount > 100) $discount = 100;
        }

        return [
            'name' => $name,
            'image' => ($image === '') ? null : $image,
            'parent_id' => $parentId,
            'final_price' => $finalPrice,
            'discount_percent' => $discount,
        ];
    }

    /**
     * Valida los datos del pack antes de guardar
     * Retorna null si es válido, o un mensaje de error si no
     */
    private function validatePack(array $data): ?string
    {
        // Validar nombre
        if (empty($data['name'])) {
            return "El nombre del pack es obligatorio";
        }
        if (mb_strlen($data['name']) < 2 || mb_strlen($data['name']) > 100) {
            return "El nombre debe tener entre 2 y 100 caracteres";
        }

        // Validar imagen (opcional)
        if ($data['image'] !== null) {
            if (mb_strlen($data['image']) > 255) {
                return "La URL de imagen es demasiado larga";
            }
            if (strpos($data['image'], 'http') === 0) {
                if (!filter_var($data['image'], FILTER_VALIDATE_URL)) {
                    return "La URL de imagen no es válida";
                }
            }
        }

        // Validar parent_id si existe
        if ($data['parent_id'] !== null && $data['parent_id'] <= 0) {
            return "ID de pack padre inválido";
        }

        // Validar final_price
        if ($data['final_price'] !== null) {
            if ($data['final_price'] < 0 || $data['final_price'] > 999999.99) {
                return "Precio del pack inválido";
            }
        }

        // Validar discount_percent
        if ($data['discount_percent'] !== null) {
            if ($data['discount_percent'] < 0 || $data['discount_percent'] > 100) {
                return "El descuento debe estar entre 0 y 100%";
            }
        }

        return null; // Todo válido
    }

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
