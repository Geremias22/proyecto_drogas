<?php
require_once 'model/conectaDB.php';

class PackModel {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance();
    }

    public function getFeatured(int $limit = 3): array {
        $limit = max(1, min($limit, 12));

        $sql = "SELECT id, name, image
                FROM packs
                ORDER BY date_create DESC
                LIMIT $limit";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // public function getById(int $id) {
    //     $sql = "SELECT id, name, image
    //             FROM packs
    //             WHERE id = :id
    //             LIMIT 1";

    //     $stmt = $this->db->prepare($sql);
    //     $stmt->execute(['id' => $id]);
    //     return $stmt->fetch(PDO::FETCH_ASSOC);
    // }

    public function getAllAdmin(): array {
        $sql = "SELECT id, name, parent_id, image, final_price, discount_percent
                FROM packs
                ORDER BY id DESC";
        $st = $this->db->prepare($sql);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id) {
        $sql = "SELECT id, name, parent_id, image, final_price, discount_percent
                FROM packs
                WHERE id = :id
                LIMIT 1";
        $st = $this->db->prepare($sql);
        $st->execute(['id' => $id]);
        return $st->fetch(PDO::FETCH_ASSOC);
    }

    public function createAdmin(array $data): bool {
        $sql = "INSERT INTO packs (name, parent_id, image, final_price, discount_percent, date_create)
                VALUES (:name, :parent_id, :image, :final_price, :discount_percent, NOW())";
        $st = $this->db->prepare($sql);
        return $st->execute([
            'name' => $data['name'],
            'parent_id' => $data['parent_id'],
            'image' => $data['image'],
            'final_price' => $data['final_price'],
            'discount_percent' => $data['discount_percent'],
        ]);
    }

    public function updateAdmin(int $id, array $data): bool {
        $sql = "UPDATE packs
                SET name = :name,
                    parent_id = :parent_id,
                    image = :image,
                    final_price = :final_price,
                    discount_percent = :discount_percent,
                    last_date = NOW()
                WHERE id = :id";
        $st = $this->db->prepare($sql);
        return $st->execute([
            'id' => $id,
            'name' => $data['name'],
            'parent_id' => $data['parent_id'],
            'image' => $data['image'],
            'final_price' => $data['final_price'],
            'discount_percent' => $data['discount_percent'],
        ]);
    }

    public function deleteAdmin(int $id): bool {
        // Si quieres, aquí puedes borrar físico + cascade en pack_products (ya lo tienes)
        $sql = "DELETE FROM packs WHERE id = :id";
        $st = $this->db->prepare($sql);
        return $st->execute(['id' => $id]);
    }
}
