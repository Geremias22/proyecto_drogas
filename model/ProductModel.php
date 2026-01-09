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
                    COALESCE(s.cantidad, 0) AS stock_qty
                FROM products p
                INNER JOIN categories c ON p.category_id = c.id
                LEFT JOIN stock s ON s.product_id = p.id
                WHERE p.is_active = 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT 
                    p.*,
                    c.name AS category_name,
                    COALESCE(s.cantidad, 0) AS stock_qty
                FROM products p
                INNER JOIN categories c ON p.category_id = c.id
                LEFT JOIN stock s ON s.product_id = p.id
                WHERE p.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => (int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
