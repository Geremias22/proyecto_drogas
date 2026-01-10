<?php
require_once 'model/conectaDB.php';

class StockModel
{
    private $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

    public function getAllWithProduct(string $q = ''): array
    {
        $q = trim($q);

        $sql = "SELECT
                    s.id AS stock_id,
                    s.product_id,
                    s.cantidad,
                    s.cantidad_min,
                    s.cantidad_max,
                    p.name AS product_name,
                    p.price,
                    p.is_active,
                    c.name AS category_name
                FROM stock s
                INNER JOIN products p ON p.id = s.product_id
                INNER JOIN categories c ON c.id = p.category_id
                WHERE 1=1";

        $params = [];

        if ($q !== '') {
            $sql .= " AND (p.name LIKE :q OR c.name LIKE :q)";
            $params['q'] = '%' . $q . '%';
        }

        $sql .= " ORDER BY p.name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByProductId(int $productId): ?array
    {
        $sql = "SELECT s.*, p.name AS product_name
                FROM stock s
                INNER JOIN products p ON p.id = s.product_id
                WHERE s.product_id = :pid
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['pid' => $productId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Ajuste absoluto: pone cantidad exacta
     * Registra movimiento ADJUST con la diferencia (puede ser + o -)
     */
    public function setQuantity(int $productId, int $newQty): bool
    {
        if ($newQty < 0) {
            throw new Exception("El stock no puede ser negativo");
        }

        $this->db->beginTransaction();

        try {
            // lock fila stock
            $sql = "SELECT id, cantidad FROM stock WHERE product_id = :pid LIMIT 1 FOR UPDATE";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['pid' => $productId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                throw new Exception("Stock no encontrado para producto $productId");
            }

            $stockId = (int)$row['id'];
            $oldQty = (int)$row['cantidad'];
            $diff = $newQty - $oldQty;

            $upd = $this->db->prepare("UPDATE stock SET cantidad = :qty WHERE id = :sid");
            $ok = $upd->execute(['qty' => $newQty, 'sid' => $stockId]);
            if (!$ok) {
                throw new Exception("No se pudo actualizar stock");
            }

            // movimiento (guardamos diff; puede ser negativo)
            $mov = $this->db->prepare("INSERT INTO stock_movements (stock_id, type, quantity) VALUES (:sid, 'ADJUST', :q)");
            $ok2 = $mov->execute(['sid' => $stockId, 'q' => $diff]);
            if (!$ok2) {
                throw new Exception("No se pudo registrar movimiento");
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Ajuste relativo: suma/resta unidades
     * type IN / OUT y quantity siempre positiva
     */
    public function changeQuantity(int $productId, int $delta, string $type): bool
    {
        if ($delta <= 0) {
            throw new Exception("La cantidad debe ser mayor que 0");
        }

        if (!in_array($type, ['IN', 'OUT'], true)) {
            throw new Exception("Tipo de movimiento inválido");
        }

        $this->db->beginTransaction();

        try {
            $sql = "SELECT id, cantidad FROM stock WHERE product_id = :pid LIMIT 1 FOR UPDATE";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['pid' => $productId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                throw new Exception("Stock no encontrado para producto $productId");
            }

            $stockId = (int)$row['id'];
            $current = (int)$row['cantidad'];

            if ($type === 'OUT' && $delta > $current) {
                throw new Exception("Stock insuficiente. Disponible: $current");
            }

            $sign = ($type === 'IN') ? +1 : -1;
            $newQty = $current + ($sign * $delta);

            $upd = $this->db->prepare("UPDATE stock SET cantidad = :qty WHERE id = :sid");
            $ok = $upd->execute(['qty' => $newQty, 'sid' => $stockId]);
            if (!$ok) {
                throw new Exception("No se pudo actualizar stock");
            }

            $mov = $this->db->prepare("INSERT INTO stock_movements (stock_id, type, quantity) VALUES (:sid, :t, :q)");
            $ok2 = $mov->execute(['sid' => $stockId, 't' => $type, 'q' => $delta]);
            if (!$ok2) {
                throw new Exception("No se pudo registrar movimiento");
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getMovementsByProduct(int $productId, int $limit = 50): array
    {
        $sql = "SELECT
                    sm.id,
                    sm.type,
                    sm.quantity,
                    sm.date
                FROM stock_movements sm
                INNER JOIN stock s ON s.id = sm.stock_id
                WHERE s.product_id = :pid
                ORDER BY sm.date DESC
                LIMIT $limit";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['pid' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
