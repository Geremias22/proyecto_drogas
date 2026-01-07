<?php
require_once 'model/conectaDB.php'; 

class UserModel {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance();
    }

    // Busca un usuario por su email
    public function getUserByEmail($email) {
        $sql = "SELECT * FROM users WHERE gmail = :email AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}