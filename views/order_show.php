<h2 class="mb-4">🧾 Pedido <?php echo htmlspecialchars($order['order_reference']); ?></h2>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title mb-3">Datos del pedido</h5>
        <div class="mb-2"><strong>Estado:</strong> <?php echo htmlspecialchars($order['status']); ?></div>
        <div class="mb-2"><strong>Pago:</strong> <?php echo htmlspecialchars($order['payment_method'] ?? '-'); ?></div>
        <div class="mb-2"><strong>Teléfono:</strong> <?php echo htmlspecialchars($order['phone']); ?></div>
        <div class="mb-2"><strong>Dirección:</strong> <?php echo htmlspecialchars($order['address']); ?></div>
        <div class="text-muted small"><strong>Fecha:</strong> <?php echo htmlspecialchars($order['created_at']); ?></div>

        <hr>
        <a class="btn btn-outline-secondary w-100" href="index.php?c=user&a=profile">Volver al perfil</a>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title mb-3">Productos</h5>

        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-dark">
              <tr>
                <th>Producto</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Precio</th>
                <th class="text-end">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $it): ?>
                <?php
                  $qty = (int)$it['quantity'];
                  $price = (float)$it['price'];
                  $sub = $qty * $price;
                ?>
                <tr>
                  <td>
                    <strong><?php echo htmlspecialchars($it['name']); ?></strong><br>
                    <small class="text-muted"><?php echo htmlspecialchars($it['category_name']); ?></small>
                  </td>
                  <td class="text-center"><?php echo $qty; ?></td>
                  <td class="text-end"><?php echo number_format($price, 2); ?>€</td>
                  <td class="text-end"><strong><?php echo number_format($sub, 2); ?>€</strong></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="border-top pt-3">
          <?php foreach ($taxBreakdown as $rate => $row): ?>
            <div class="d-flex justify-content-between">
              <span class="text-muted"><?php echo htmlspecialchars($row['name']); ?> (<?php echo number_format((float)$rate, 2); ?>%) — Base</span>
              <strong><?php echo number_format((float)$row['base'], 2); ?>€</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">IVA</span>
              <strong><?php echo number_format((float)$row['tax'], 2); ?>€</strong>
            </div>
          <?php endforeach; ?>

          <div class="d-flex justify-content-between mt-2">
            <span class="h5 mb-0">Total</span>
            <span class="h4 text-success mb-0"><?php echo number_format((float)$grandTotal, 2); ?>€</span>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
