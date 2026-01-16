<?php
require_once 'controller/auth_controller.php';
require_once 'model/ProductModel.php';
require_once 'model/CategoryModel.php';
require_once 'model/SupplierModel.php';
require_once 'helpers/SecurityHelper.php';

class AdminProductController
{
    public function index()
    {
        AuthController::checkAdmin();

        $productModel = new ProductModel();
        $products = $productModel->getAllAdmin(); // lo añadimos en el modelo

        ob_start();
        require 'views/admin/products/index.php';
        $viewContent = ob_get_clean();
        require 'views/layouts/main.php';
    }

    public function create()
    {
        AuthController::checkAdmin();

        $categoryModel = new CategoryModel();
        $supplierModel = new SupplierModel();

        $categories = $categoryModel->getAll();
        $suppliers  = $supplierModel->getAll();

        $product = null; // reutilizamos form
        $mode = 'create';

        ob_start();
        require 'views/admin/products/form.php';
        $viewContent = ob_get_clean();
        require 'views/layouts/main.php';
    }

    public function store()
    {
        AuthController::checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=admin_product&a=index");
            exit();
        }

        // Validar CSRF token
        $csrf_token = trim($_POST['csrf_token'] ?? '');
        if (!SecurityHelper::validateCsrfToken($csrf_token)) {
            header("Location: index.php?c=admin_product&a=create&error=" . urlencode("Solicitud inválida (CSRF)"));
            exit();
        }

        $data = $this->readProductPost();
        
        // Validación server-side
        $error = $this->validateProduct($data);
        if ($error) {
            header("Location: index.php?c=admin_product&a=create&error=" . urlencode($error));
            exit();
        }

        $productModel = new ProductModel();
        $ok = $productModel->createAdmin($data);

        if ($ok) {
            header("Location: index.php?c=admin_product&a=index&msg=Producto creado");
        } else {
            header("Location: index.php?c=admin_product&a=create&error=No se pudo crear el producto");
        }
        exit();
    }

    public function edit()
    {
        AuthController::checkAdmin();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header("Location: index.php?c=admin_product&a=index&error=ID inválido");
            exit();
        }

        $productModel = new ProductModel();
        $product = $productModel->getById($id);

        if (!$product) {
            header("Location: index.php?c=admin_product&a=index&error=Producto no encontrado");
            exit();
        }

        $categoryModel = new CategoryModel();
        $supplierModel = new SupplierModel();

        $categories = $categoryModel->getAll();
        $suppliers  = $supplierModel->getAll();

        $mode = 'edit';

        ob_start();
        require 'views/admin/products/form.php';
        $viewContent = ob_get_clean();
        require 'views/layouts/main.php';
    }

    public function update()
    {
        AuthController::checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=admin_product&a=index");
            exit();
        }

        // Validar CSRF token
        $csrf_token = trim($_POST['csrf_token'] ?? '');
        if (!SecurityHelper::validateCsrfToken($csrf_token)) {
            header("Location: index.php?c=admin_product&a=index&error=" . urlencode("Solicitud inválida (CSRF)"));
            exit();
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            header("Location: index.php?c=admin_product&a=index&error=ID inválido");
            exit();
        }

        $data = $this->readProductPost();
        
        // Validación server-side
        $error = $this->validateProduct($data);
        if ($error) {
            header("Location: index.php?c=admin_product&a=edit&id=$id&error=" . urlencode($error));
            exit();
        }

        $productModel = new ProductModel();
        $ok = $productModel->updateAdmin($id, $data);

        if ($ok) {
            header("Location: index.php?c=admin_product&a=index&msg=Producto actualizado");
        } else {
            header("Location: index.php?c=admin_product&a=edit&id=$id&error=No se pudo actualizar");
        }
        exit();
    }

    public function delete()
    {
        AuthController::checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=admin_product&a=index");
            exit();
        }

        // Validar CSRF token
        $csrf_token = trim($_POST['csrf_token'] ?? '');
        if (!SecurityHelper::validateCsrfToken($csrf_token)) {
            header("Location: index.php?c=admin_product&a=index&error=" . urlencode("Solicitud inválida (CSRF)"));
            exit();
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            header("Location: index.php?c=admin_product&a=index&error=ID inválido");
            exit();
        }

        $productModel = new ProductModel();
        $ok = $productModel->deleteAdmin($id);

        if ($ok) {
            header("Location: index.php?c=admin_product&a=index&msg=Producto eliminado");
        } else {
            header("Location: index.php?c=admin_product&a=index&error=No se pudo eliminar");
        }
        exit();
    }

    private function readProductPost(): array
    {
        // Sanitizamos por tipo (esto evita problemas típicos; la SQL injection la evitas con prepared statements)
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $supplierId = ($_POST['supplier_id'] ?? '') === '' ? null : (int)$_POST['supplier_id'];
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        return [
            'category_id' => $categoryId,
            'supplier_id' => $supplierId,
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'is_active' => $isActive,
        ];
    }

    /**
     * Valida los datos del producto antes de guardar
     * Retorna null si es válido, o un mensaje de error si no
     */
    private function validateProduct(array $data): ?string
    {
        // Validar nombre
        if (empty($data['name'])) {
            return "El nombre es obligatorio";
        }
        if (mb_strlen($data['name']) < 3 || mb_strlen($data['name']) > 100) {
            return "El nombre debe tener entre 3 y 100 caracteres";
        }

        // Validar descripción (opcional pero si existe, validar)
        if (!empty($data['description']) && mb_strlen($data['description']) > 1000) {
            return "La descripción no puede exceder 1000 caracteres";
        }

        // Validar precio
        if ($data['price'] < 0) {
            return "El precio no puede ser negativo";
        }
        if ($data['price'] > 999999.99) {
            return "El precio es demasiado alto";
        }

        // Validar categoría (obligatoria)
        if ($data['category_id'] <= 0) {
            return "Debes seleccionar una categoría";
        }

        // Validar is_active
        if ($data['is_active'] !== 0 && $data['is_active'] !== 1) {
            return "Estado del producto inválido";
        }

        return null; // Todo válido
    }
}
