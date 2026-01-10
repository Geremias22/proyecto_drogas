<?php
require_once 'controller/auth_controller.php';
require_once 'model/CategoryModel.php';
require_once 'model/TaxModel.php';

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

        $data = $this->readPost();

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

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            header("Location: index.php?c=admin_category&a=index&error=ID inválido");
            exit();
        }

        $data = $this->readPost();

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
}
