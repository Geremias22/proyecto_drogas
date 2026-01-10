<?php
require_once 'model/conectaDB.php';

class ProductModel {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance();
    }

    public function getAllActive() {
        $sql = "SELECT 
                    p.*,
                    c.name AS category_name,
                    COALESCE(s.cantidad, 0) AS stock_qty,
                    t.name AS tax_name,
                    t.rate_iva AS tax_rate
                FROM products p
                INNER JOIN categories c ON p.category_id = c.id
                LEFT JOIN stock s ON s.product_id = p.id
                LEFT JOIN taxes t ON c.tax_id = t.id
                WHERE p.is_active = 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT 
                    p.*,
                    c.name AS category_name,
                    COALESCE(s.cantidad, 0) AS stock_qty,
                    t.name AS tax_name,
                    t.rate_iva AS tax_rate
                FROM products p
                INNER JOIN categories c ON p.category_id = c.id
                LEFT JOIN stock s ON s.product_id = p.id
                LEFT JOIN taxes t ON c.tax_id = t.id
                WHERE p.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => (int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function searchActive(string $q = '', ?int $categoryId = null): array {
        $q = trim($q);

        $sql = "SELECT 
                    p.*,
                    c.name as category_name,
                    COALESCE(s.cantidad, 0) AS stock_qty
                FROM products p
                INNER JOIN categories c ON p.category_id = c.id
                LEFT JOIN stock s ON s.product_id = p.id
                WHERE p.is_active = 1";

        $params = [];

        if ($q !== '') {
            $sql .= " AND (p.name LIKE :q OR p.description LIKE :q)";
            $params['q'] = '%' . $q . '%';
        }

        if ($categoryId !== null && $categoryId > 0) {
            $sql .= " AND p.category_id = :cat";
            $params['cat'] = $categoryId;
        }

        $sql .= " ORDER BY p.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllAdmin(): array
    {
        $sql = "SELECT
                p.*,
                c.name AS category_name,
                s.name AS supplier_name,
                COALESCE(st.cantidad, 0) AS stock_qty
                FROM products p
                INNER JOIN categories c ON c.id = p.category_id
                LEFT JOIN suppliers s ON s.id = p.supplier_id
                LEFT JOIN stock st ON st.product_id = p.id
                ORDER BY p.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createAdmin(array $data): bool
    {
        $sql = "INSERT INTO products (category_id, supplier_id, name, description, price, is_active)
                VALUES (:category_id, :supplier_id, :name, :description, :price, :is_active)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'category_id' => (int)$data['category_id'],
            'supplier_id' => $data['supplier_id'], // puede ser null
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'is_active' => (int)$data['is_active'],
        ]);
    }

    public function updateAdmin(int $id, array $data): bool
    {
        $sql = "UPDATE products
                SET category_id = :category_id,
                    supplier_id = :supplier_id,
                    name = :name,
                    description = :description,
                    price = :price,
                    is_active = :is_active,
                    last_date = NOW()
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'category_id' => (int)$data['category_id'],
            'supplier_id' => $data['supplier_id'],
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'is_active' => (int)$data['is_active'],
        ]);
    }

    // Para mantener integridad con order_products, mejor "baja lógica" (desactivar) en vez de borrar físico
    public function deleteAdmin(int $id): bool
    {
        $sql = "UPDATE products SET is_active = 0, last_date = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }


}
