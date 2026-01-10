<h2 class="mb-4">🧾 Mis pedidos</h2>

<?php if (empty($orders)): ?>
  <div class="alert alert-info">Aún no has realizado pedidos.</div>
<?php else: ?>
  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead class="table-dark">
          <tr>
            <th>Referencia</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $o): ?>
            <tr>
              <td><strong><?php echo htmlspecialchars($o['order_reference']); ?></strong></td>
              <td><?php echo htmlspecialchars($o['status']); ?></td>
              <td><?php echo htmlspecialchars($o['created_at']); ?></td>
              <td class="text-end">
                <a class="btn btn-outline-primary btn-sm"
                   href="index.php?c=order&a=show&id=<?php echo (int)$o['id']; ?>">
                  Ver detalle
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>
