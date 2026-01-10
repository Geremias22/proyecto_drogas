<?php
require_once 'model/conectaDB.php';

class CategoryModel {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance();
    }

    /**
     * Categorías principales (parent_id NULL)
     */
    public function getMainCategories(): array {
        $sql = "SELECT id, name, image
                FROM categories
                WHERE parent_id IS NULL
                ORDER BY name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Todas las categorías (por si luego haces admin o sidebar)
     */
    public function getAll(): array {
        $sql = "SELECT id, name, parent_id, image, tax_id
                FROM categories
                ORDER BY parent_id ASC, name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // public function getById(int $id) {
    //     $sql = "SELECT id, name FROM categories WHERE id = :id LIMIT 1";
    //     $stmt = $this->db->prepare($sql);
    //     $stmt->execute(['id' => $id]);
    //     return $stmt->fetch(PDO::FETCH_ASSOC);
    // }

    public function getAllWithParentAndTax(): array
    {
        $sql = "SELECT
                c.id, c.name, c.image, c.parent_id, c.tax_id, c.date_create,
                p.name AS parent_name,
                t.name AS tax_name,
                t.rate_iva AS tax_rate
                FROM categories c
                LEFT JOIN categories p ON p.id = c.parent_id
                LEFT JOIN taxes t ON t.id = c.tax_id
                ORDER BY COALESCE(c.parent_id, c.id), c.name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id)
    {
        $sql = "SELECT id, name, parent_id, image, tax_id
                FROM categories
                WHERE id = :id
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllExcluding(int $excludeId): array
    {
        $sql = "SELECT id, name
                FROM categories
                WHERE id <> :id
                ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $excludeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createAdmin(array $data): bool
    {
        if (trim($data['name'] ?? '') === '') return false;

        $sql = "INSERT INTO categories (name, parent_id, image, tax_id)
                VALUES (:name, :parent_id, :image, :tax_id)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'name' => $data['name'],
            'parent_id' => $data['parent_id'],
            'image' => $data['image'],
            'tax_id' => $data['tax_id'],
        ]);
    }

    public function updateAdmin(int $id, array $data): bool
    {
        if (trim($data['name'] ?? '') === '') return false;

        // Si elige a sí misma como padre -> lo anulamos
        if ($data['parent_id'] !== null && (int)$data['parent_id'] === $id) {
            $data['parent_id'] = null;
        }

        $sql = "UPDATE categories
                SET name = :name,
                    parent_id = :parent_id,
                    image = :image,
                    tax_id = :tax_id,
                    last_date = NOW()
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'parent_id' => $data['parent_id'],
            'image' => $data['image'],
            'tax_id' => $data['tax_id'],
        ]);
    }

    public function deleteAdmin(int $id): void
    {
        // 1) si hay hijos, no borramos
        $sqlChild = "SELECT 1 FROM categories WHERE parent_id = :id LIMIT 1";
        $st = $this->db->prepare($sqlChild);
        $st->execute(['id' => $id]);
        if ($st->fetchColumn()) {
            throw new Exception("No se puede borrar: tiene subcategorías.");
        }

        // 2) si hay productos usando esa categoría, no borramos
        $sqlProd = "SELECT 1 FROM products WHERE category_id = :id LIMIT 1";
        $st2 = $this->db->prepare($sqlProd);
        $st2->execute(['id' => $id]);
        if ($st2->fetchColumn()) {
            throw new Exception("No se puede borrar: hay productos asignados.");
        }

        $sql = "DELETE FROM categories WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
    }


}
