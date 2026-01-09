<?php
require_once 'model/conectaDB.php';

class OrderModel {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance();
    }

    /**
     * Crea un pedido desde el carrito de sesión:
     * - Inserta en orders
     * - Inserta líneas en order_products
     * - Descuenta stock
     * - Inserta movimiento OUT en stock_movements
     *
     * @throws Exception si falta stock o hay error SQL
     * @return string order_reference
     */
    public function createFromCart(int $userId, string $phone, string $address, array $cart): string
    {
        if (empty($cart)) {
            throw new Exception("El carrito está vacío");
        }

        // Normalizamos cantidades
        foreach ($cart as $pid => $qty) {
            $qty = (int)$qty;
            if ($qty <= 0) {
                throw new Exception("Cantidad inválida para producto $pid");
            }
        }

        $this->db->beginTransaction();

        try {
            $orderRef = $this->generateUniqueReference();

            // 1) Insert order
            $sqlOrder = "INSERT INTO orders (user_id, order_reference, status, phone, address)
                         VALUES (:user_id, :ref, 'pending', :phone, :address)";
            $stmtOrder = $this->db->prepare($sqlOrder);
            $this->execOrThrow($stmtOrder, [
                'user_id' => $userId,
                'ref'     => $orderRef,
                'phone'   => $phone,
                'address' => $address
            ], "No se pudo crear el pedido");

            $orderId = (int)$this->db->lastInsertId();

            // 2) Por cada producto: lock stock, validar, insertar línea, descontar, movimiento
            foreach ($cart as $productId => $qty) {
                $productId = (int)$productId;
                $qty = (int)$qty;

                // Lock de fila de stock para evitar carreras
                $sqlCheck = "SELECT 
                                p.id AS product_id,
                                p.price AS unit_price,
                                p.is_active,
                                s.id AS stock_id,
                                s.cantidad AS stock_qty
                             FROM products p
                             INNER JOIN stock s ON s.product_id = p.id
                             WHERE p.id = :pid
                             LIMIT 1
                             FOR UPDATE";

                $stmtCheck = $this->db->prepare($sqlCheck);
                $this->execOrThrow($stmtCheck, ['pid' => $productId], "Error comprobando stock");

                $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                if (!$row) {
                    throw new Exception("Producto no encontrado o sin stock asociado (ID: $productId)");
                }
                if ((int)$row['is_active'] !== 1) {
                    throw new Exception("Producto inactivo (ID: $productId)");
                }

                $available = (int)$row['stock_qty'];
                if ($qty > $available) {
                    throw new Exception("Stock insuficiente (Producto $productId). Disponible: $available, Pedido: $qty");
                }

                $unitPrice = (float)$row['unit_price'];
                $stockId   = (int)$row['stock_id'];

                // Insert línea
                $sqlLine = "INSERT INTO order_products (order_id, product_id, quantity, price)
                            VALUES (:oid, :pid, :qty, :price)";
                $stmtLine = $this->db->prepare($sqlLine);
                $this->execOrThrow($stmtLine, [
                    'oid'   => $orderId,
                    'pid'   => $productId,
                    'qty'   => $qty,
                    'price' => $unitPrice
                ], "No se pudo insertar línea del pedido (producto $productId)");

                // Descontar stock
                $sqlUpd = "UPDATE stock SET cantidad = cantidad - :qty WHERE id = :sid";
                $stmtUpd = $this->db->prepare($sqlUpd);
                $this->execOrThrow($stmtUpd, [
                    'qty' => $qty,
                    'sid' => $stockId
                ], "No se pudo actualizar stock (producto $productId)");

                // Movimiento OUT
                $sqlMov = "INSERT INTO stock_movements (stock_id, type, quantity)
                           VALUES (:sid, 'OUT', :qty)";
                $stmtMov = $this->db->prepare($sqlMov);
                $this->execOrThrow($stmtMov, [
                    'sid' => $stockId,
                    'qty' => $qty
                ], "No se pudo registrar movimiento de stock (producto $productId)");
            }

            $this->db->commit();
            return $orderRef;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // -------------------------
    // Helpers
    // -------------------------

    private function generateUniqueReference(): string
    {
        // Ej: EPC-20260108173522-4821
        do {
            $ref = 'EPC-' . date('YmdHis') . '-' . random_int(1000, 9999);
            $sql = "SELECT 1 FROM orders WHERE order_reference = :ref LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['ref' => $ref]);
            $exists = (bool)$stmt->fetchColumn();
        } while ($exists);

        return $ref;
    }

    private function execOrThrow(PDOStatement $stmt, array $params, string $errorMessage): void
    {
        $ok = $stmt->execute($params);
        if (!$ok) {
            $info = $stmt->errorInfo();
            $detail = $info[2] ?? 'Error desconocido';
            throw new Exception($errorMessage . " | " . $detail);
        }
    }
}
