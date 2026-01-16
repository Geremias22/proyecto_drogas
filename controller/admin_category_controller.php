<?php
require_once 'controller/auth_controller.php';
require_once 'model/CategoryModel.php';
require_once 'model/TaxModel.php';
require_once 'helpers/SecurityHelper.php';

class AdminCategoryController
{
    public function index()
    {
        AuthController::checkAdmin();

        $categoryModel = new CategoryModel();
        $categories = $categoryModel->getAllWithParentAndTax();

        ob_start();
        require 'views/admin/categories/index.php';
        $viewContent = ob_get_clean();
        require 'views/layouts/main.php';
    }

    public function create()
    {
        AuthController::checkAdmin();

        $categoryModel = new CategoryModel();
        $taxModel = new TaxModel();

        $parents = $categoryModel->getAll(); // para elegir parent
        $taxes   = $taxModel->getAll();

        $category = null;
        $mode = 'create';

        ob_start();
        require 'views/admin/categories/form.php';
        $viewContent = ob_get_clean();
        require 'views/layouts/main.php';
    }

    public function store()
    {
        AuthController::checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=admin_category&a=index");
            exit();
        }

        // Validar CSRF token
        $csrf_token = trim($_POST['csrf_token'] ?? '');
        if (!SecurityHelper::validateCsrfToken($csrf_token)) {
            header("Location: index.php?c=admin_category&a=create&error=" . urlencode("Solicitud inválida (CSRF)"));
            exit();
        }

        $data = $this->readPost();
        
        // Validación server-side
        $error = $this->validateCategory($data);
        if ($error) {
            header("Location: index.php?c=admin_category&a=create&error=" . urlencode($error));
            exit();
        }

        $categoryModel = new CategoryModel();
        $ok = $categoryModel->createAdmin($data);

        if ($ok) {
            header("Location: index.php?c=admin_category&a=index&msg=Categoría creada");
        } else {
            header("Location: index.php?c=admin_category&a=create&error=No se pudo crear la categoría");
        }
        exit();
    }

    public function edit()
    {
        AuthController::checkAdmin();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header("Location: index.php?c=admin_category&a=index&error=ID inválido");
            exit();
        }

        $categoryModel = new CategoryModel();
        $taxModel = new TaxModel();

        $category = $categoryModel->getById($id);
        if (!$category) {
            header("Location: index.php?c=admin_category&a=index&error=Categoría no encontrada");
            exit();
        }

        $parents = $categoryModel->getAllExcluding($id);
        $taxes   = $taxModel->getAll();
        $mode = 'edit';

        ob_start();
        require 'views/admin/categories/form.php';
        $viewContent = ob_get_clean();
        require 'views/layouts/main.php';
    }

    public function update()
    {
        AuthController::checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=admin_category&a=index");
            exit();
        }

        // Validar CSRF token
        $csrf_token = trim($_POST['csrf_token'] ?? '');
        if (!SecurityHelper::validateCsrfToken($csrf_token)) {
            header("Location: index.php?c=admin_category&a=index&error=" . urlencode("Solicitud inválida (CSRF)"));
            exit();
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            header("Location: index.php?c=admin_category&a=index&error=ID inválido");
            exit();
        }

        $data = $this->readPost();
        
        // Validación server-side
        $error = $this->validateCategory($data);
        if ($error) {
            header("Location: index.php?c=admin_category&a=edit&id=$id&error=" . urlencode($error));
            exit();
        }

        $categoryModel = new CategoryModel();
        $ok = $categoryModel->updateAdmin($id, $data);

        if ($ok) {
            header("Location: index.php?c=admin_category&a=index&msg=Categoría actualizada");
        } else {
            header("Location: index.php?c=admin_category&a=edit&id=$id&error=No se pudo actualizar");
        }
        exit();
    }

    public function delete()
    {
        AuthController::checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=admin_category&a=index");
            exit();
        }

        // Validar CSRF token
        $csrf_token = trim($_POST['csrf_token'] ?? '');
        if (!SecurityHelper::validateCsrfToken($csrf_token)) {
            header("Location: index.php?c=admin_category&a=index&error=" . urlencode("Solicitud inválida (CSRF)"));
            exit();
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            header("Location: index.php?c=admin_category&a=index&error=ID inválido");
            exit();
        }

        $categoryModel = new CategoryModel();

        try {
            $categoryModel->deleteAdmin($id);
            header("Location: index.php?c=admin_category&a=index&msg=Categoría eliminada");
        } catch (Exception $e) {
            header("Location: index.php?c=admin_category&a=index&error=" . urlencode($e->getMessage()));
        }
        exit();
    }

    private function readPost(): array
    {
        $name = trim($_POST['name'] ?? '');
        $image = trim($_POST['image'] ?? '');

        $parentId = ($_POST['parent_id'] ?? '') === '' ? null : (int)$_POST['parent_id'];
        $taxId    = ($_POST['tax_id'] ?? '') === '' ? null : (int)$_POST['tax_id'];

        return [
            'name' => $name,
            'image' => ($image === '') ? null : $image,
            'parent_id' => $parentId,
            'tax_id' => $taxId,
        ];
    }

    /**
     * Valida los datos de la categoría antes de guardar
     * Retorna null si es válido, o un mensaje de error si no
     */
    private function validateCategory(array $data): ?string
    {
        // Validar nombre
        if (empty($data['name'])) {
            return "El nombre es obligatorio";
        }
        if (mb_strlen($data['name']) < 2 || mb_strlen($data['name']) > 100) {
            return "El nombre debe tener entre 2 y 100 caracteres";
        }

        // Validar imagen (opcional, pero si existe validar formato URL)
        if ($data['image'] !== null) {
            if (mb_strlen($data['image']) > 255) {
                return "La URL de imagen es demasiado larga";
            }
            // Validar que sea una URL válida (solo si no es ruta local)
            if (strpos($data['image'], 'http') === 0) {
                if (!filter_var($data['image'], FILTER_VALIDATE_URL)) {
                    return "La URL de imagen no es válida";
                }
            }
        }

        // Validar parent_id si existe
        if ($data['parent_id'] !== null && $data['parent_id'] <= 0) {
            return "ID de categoría padre inválido";
        }

        // Validar tax_id si existe
        if ($data['tax_id'] !== null && $data['tax_id'] <= 0) {
            return "ID de impuesto inválido";
        }

        return null; // Todo válido
    }
}
