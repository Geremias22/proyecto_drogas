<?php
require_once 'model/conectaDB.php';

class UserModel
{
    private $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

    public function getUserByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE gmail = :email AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function emailExists($email)
    {
        $sql = "SELECT id FROM users WHERE gmail = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return (bool) $stmt->fetchColumn();
    }

    public function create($name, $email, $password, $edad = null)
    {
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

    public function getAll()
    {
        $sql = "SELECT id, name, role, gmail, edad, is_active, date_create
                FROM users ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id)
    {
        $sql = "SELECT id, name, gmail, role, edad, img, date_create, last_date, is_active
                FROM users
                WHERE id = :id
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateRole($id, $role)
    {
        $allowed = ['customer', 'admin'];
        if (!in_array($role, $allowed, true)) return false;

        $sql = "UPDATE users SET role = :role, last_date = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['role' => $role, 'id' => (int)$id]);
    }

    public function setActive($id, $active)
    {
        $sql = "UPDATE users SET is_active = :active, last_date = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['active' => (int)$active, 'id' => (int)$id]);
    }

    public function setSecurityQA(int $userId, string $question, string $answerHash): bool
    {
        $sql = "UPDATE users
            SET security_question = :q,
                security_answer_hash = :h,
                last_date = NOW()
            WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'q' => $question,
            'h' => $answerHash,
            'id' => $userId
        ]);
    }

    public function getSecurityQuestionByEmail(string $email): ?array
    {
        $sql = "SELECT id, security_question
            FROM users
            WHERE gmail = :email AND is_active = 1
            LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getSecurityById(int $id): ?array
    {
        $sql = "SELECT id, gmail, security_question, security_answer_hash
            FROM users
            WHERE id = :id AND is_active = 1
            LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function updatePasswordHash(int $id, string $hash): bool
    {
        $sql = "UPDATE users SET password = :pwd, last_date = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['pwd' => $hash, 'id' => $id]);
    }
}
