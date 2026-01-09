<?php
require_once 'model/conectaDB.php';

class UserModel {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance();
    }

    public function getUserByEmail($email) {
        $sql = "SELECT * FROM users WHERE gmail = :email AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function emailExists($email) {
        $sql = "SELECT id FROM users WHERE gmail = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return (bool) $stmt->fetchColumn();
    }

    public function create($name, $email, $password, $edad = null) {
        // Consejo: usa password_hash (abajo te digo cómo)
        $sql = "INSERT INTO users (name, role, gmail, password, edad, is_active)
                VALUES (:name, 'customer', :gmail, :password, :edad, 1)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name' => $name,
            'gmail' => $email,
            'password' => $password,
            'edad' => $edad
        ]);
        return $this->db->lastInsertId();
    }

    public function getAll() {
        $sql = "SELECT id, name, role, gmail, edad, is_active, date_create
                FROM users ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT id, name, role, gmail, edad, is_active FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => (int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateRole($id, $role) {
        $allowed = ['customer', 'admin'];
        if (!in_array($role, $allowed, true)) return false;

        $sql = "UPDATE users SET role = :role, last_date = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['role' => $role, 'id' => (int)$id]);
    }

    public function setActive($id, $active) {
        $sql = "UPDATE users SET is_active = :active, last_date = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['active' => (int)$active, 'id' => (int)$id]);
    }
}
