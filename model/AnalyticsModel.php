<?php
require_once 'model/conectaDB.php';

class AnalyticsModel
{
    private $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

    // Ajusta aquí qué consideras "finalizado"
    private function paidWhere(): string
    {
        // Ejemplo: status = 'paid'
        // Si aún no lo tienes, temporalmente usa: status != 'pending'
        return "o.status != 'pending'";
    }

    public function kpis(string $from, string $to): array
    {
        $sql = "SELECT
                  COUNT(DISTINCT o.id) AS orders_count,
                  COALESCE(SUM(op.quantity * op.price), 0) AS net_total
                FROM orders o
                INNER JOIN order_products op ON op.order_id = o.id
                WHERE " . $this->paidWhere() . "
                  AND o.created_at BETWEEN :from AND :to";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['from' => $from, 'to' => $to]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        $orders = (int)($row['orders_count'] ?? 0);
        $net = (float)($row['net_total'] ?? 0);

        return [
            'orders_count' => $orders,
            'net_total' => $net,
            'avg_ticket' => $orders > 0 ? ($net / $orders) : 0,
        ];
    }

    public function topProducts(string $from, string $to, int $limit = 5): array
    {
        $sql = "SELECT
                  p.id,
                  p.name,
                  SUM(op.quantity) AS units,
                  SUM(op.quantity * op.price) AS revenue
                FROM orders o
                INNER JOIN order_products op ON op.order_id = o.id
                INNER JOIN products p ON p.id = op.product_id
                WHERE " . $this->paidWhere() . "
                  AND o.created_at BETWEEN :from AND :to
                GROUP BY p.id, p.name
                ORDER BY units DESC
                LIMIT $limit";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['from' => $from, 'to' => $to]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function salesByDay(string $from, string $to): array
    {
        $sql = "SELECT
                  DATE(o.created_at) AS day,
                  SUM(op.quantity * op.price) AS revenue
                FROM orders o
                INNER JOIN order_products op ON op.order_id = o.id
                WHERE " . $this->paidWhere() . "
                  AND o.created_at BETWEEN :from AND :to
                GROUP BY DATE(o.created_at)
                ORDER BY day ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['from' => $from, 'to' => $to]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function salesByCategory(string $from, string $to): array
    {
        $sql = "SELECT
                  c.name AS category,
                  SUM(op.quantity * op.price) AS revenue
                FROM orders o
                INNER JOIN order_products op ON op.order_id = o.id
                INNER JOIN products p ON p.id = op.product_id
                INNER JOIN categories c ON c.id = p.category_id
                WHERE " . $this->paidWhere() . "
                  AND o.created_at BETWEEN :from AND :to
                GROUP BY c.name
                ORDER BY revenue DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['from' => $from, 'to' => $to]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
