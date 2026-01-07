<?php
require_once 'model/conectaDB.php';

class ProductModel {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance();
    }

    public function getAllActive() {
        // Hacemos un JOIN para saber el nombre de la categoria
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p
                INNER JOIN categories c ON p.category_id = c.id
                WHERE p.is_active = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}