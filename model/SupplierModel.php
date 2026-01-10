<?php
require_once 'model/conectaDB.php';

class SupplierModel {
    private $db;
    public function __construct() { $this->db = DB::getInstance(); }

    public function getAll(): array {
        $sql = "SELECT id, name, code FROM suppliers ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
