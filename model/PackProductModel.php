<?php
require_once 'model/conectaDB.php';

class PackProductModel {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance();
    }

    /**
     * Devuelve los productos de un pack con su cantidad (qty) y datos del producto
     */
    public function getProductsByPackId(int $packId): array {
        $sql = "SELECT 
                    p.id,
                    p.name,
                    p.description,
                    p.price,
                    p.is_active,
                    c.name AS category_name,
                    pp.qty,
                    COALESCE(s.cantidad, 0) AS stock_qty
                FROM pack_products pp
                INNER JOIN products p ON p.id = pp.product_id
                INNER JOIN categories c ON c.id = p.category_id
                LEFT JOIN stock s ON s.product_id = p.id
                WHERE pp.pack_id = :pack_id
                ORDER BY p.name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['pack_id' => $packId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getItems(int $packId): array {
        $sql = "SELECT
                  pp.pack_id,
                  pp.product_id,
                  pp.qty,
                  p.name,
                  p.price,
                  c.name AS category_name
                FROM pack_products pp
                INNER JOIN products p ON p.id = pp.product_id
                INNER JOIN categories c ON c.id = p.category_id
                WHERE pp.pack_id = :pid
                ORDER BY p.name ASC";
        $st = $this->db->prepare($sql);
        $st->execute(['pid' => $packId]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function upsertItem(int $packId, int $productId, int $qty): bool {
        $qty = max(1, $qty);

        $sql = "INSERT INTO pack_products (pack_id, product_id, qty)
                VALUES (:pack_id, :product_id, :qty)
                ON DUPLICATE KEY UPDATE qty = VALUES(qty)";
        $st = $this->db->prepare($sql);
        return $st->execute([
            'pack_id' => $packId,
            'product_id' => $productId,
            'qty' => $qty,
        ]);
    }

    public function removeItem(int $packId, int $productId): bool {
        $sql = "DELETE FROM pack_products WHERE pack_id = :pack_id AND product_id = :product_id";
        $st = $this->db->prepare($sql);
        return $st->execute([
            'pack_id' => $packId,
            'product_id' => $productId,
        ]);
    }
}
