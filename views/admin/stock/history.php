<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h2 class="mb-0">🧾 Historial de stock</h2>
    <div class="text-muted">
      Producto: <strong><?php echo htmlspecialchars($stock['product_name']); ?></strong>
      · Stock actual: <strong><?php echo (int)$stock['cantidad']; ?></strong>
    </div>
  </div>

  <a class="btn btn-outline-secondary" href="index.php?c=stock&a=index"><i class="fa-solid fa-arrow-left me-2"></i>Volver</a>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Tipo</th>
          <th class="text-end">Cantidad</th>
          <th>Fecha</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($movements as $m): ?>
          <tr>
            <td><?php echo (int)$m['id']; ?></td>
            <td>
              <?php
                $t = $m['type'];
                $badge = 'secondary';
                if ($t === 'IN') $badge = 'success';
                if ($t === 'OUT') $badge = 'danger';
                if ($t === 'ADJUST') $badge = 'primary';
              ?>
              <span class="badge bg-<?php echo $badge; ?>"><?php echo htmlspecialchars($t); ?></span>
            </td>
            <td class="text-end">
              <?php echo (int)$m['quantity']; ?>
            </td>
            <td><?php echo htmlspecialchars($m['date']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
