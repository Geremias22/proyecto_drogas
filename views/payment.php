<h2 class="mb-4"> Finalizar Pedido</h2>

<?php if(isset($_GET['error'])): ?>
  <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>

<div class="row g-4">
  <!-- Columna izquierda: datos -->
  <div class="col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title mb-3">Datos de entrega</h5>

        <form action="index.php?c=order&a=place" method="POST">
          <div class="mb-3">
            <label class="form-label">Teléfono</label>
            <input type="text" name="phone" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Dirección</label>
            <input type="text" name="address" class="form-control" required>
          </div>

          <!-- Método de pago -->
          <div class="mb-3">
            <label class="form-label">Método de pago</label>
            <select name="payment_method" class="form-select">
              <option value="cash">Efectivo</option>
              <option value="card">Tarjeta</option>
              <option value="bizum">Bizum</option>
            </select>
            <div class="form-text text-muted">Para la práctica (no se procesa pago real).</div>
          </div>

          <button type="submit" class="btn btn-success w-100 btn-lg">
            Confirmar pedido
          </button>

          <a href="index.php?c=cart&a=index" class="btn btn-outline-secondary w-100 mt-2">
            Volver al carrito
          </a>
        </form>
      </div>
    </div>
  </div>

  <!-- Columna derecha: resumen / ticket -->
  <div class="col-lg-7">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title mb-3">Resumen</h5>

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
              <?php foreach ($productsInCart as $p): ?>
                <tr>
                  <td>
                    <strong><?php echo htmlspecialchars($p['name']); ?></strong><br>
                    <small class="text-muted"><?php echo htmlspecialchars($p['category_name']); ?></small>
                  </td>
                  <td class="text-center"><?php echo (int)$p['quantity']; ?></td>
                  <td class="text-end"><?php echo number_format((float)$p['price'], 2); ?>€</td>
                  <td class="text-end"><strong><?php echo number_format((float)$p['subtotal'], 2); ?>€</strong></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <?php
          // IVA ficticio general 21% (si luego quieres por categoría, se mejora)
          $ivaRate = 0.21;
          $base = (float)$total;
          $iva = $base * $ivaRate;
          $grandTotal = $base + $iva;
        ?>

        <div class="border-top pt-3">
          <?php foreach ($taxBreakdown as $rate => $row): ?>
            <div class="d-flex justify-content-between">
              <span class="text-muted">
                <?php echo htmlspecialchars($row['name']); ?> (<?php echo number_format((float)$rate, 2); ?>%)
              </span>
              <strong><?php echo number_format($row['base'], 2); ?>€</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">IVA</span>
              <strong><?php echo number_format($row['tax'], 2); ?>€</strong>
            </div>
          <?php endforeach; ?>

          <div class="d-flex justify-content-between mt-2">
            <span class="h5 mb-0">Total</span>
            <span class="h4 text-success mb-0"><?php echo number_format($grandTotal, 2); ?>€</span>
          </div>
        </div>


        <div class="alert alert-info mt-3 mb-0">
          <strong>Preview ticket:</strong> al confirmar, se crea el pedido, se descuenta stock y se guarda en tu historial.
        </div>
      </div>
    </div>
  </div>
</div>
