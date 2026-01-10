<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h2 class="mb-0">📊 Análisis</h2>
    <div class="text-muted">Rango: últimos <?php echo (int)($_GET['days'] ?? 14); ?> días</div>
  </div>

  <form method="GET" action="index.php" class="d-flex align-items-center gap-2">
    <input type="hidden" name="c" value="admin_analytics">
    <input type="hidden" name="a" value="index">
    <select name="days" class="form-select" style="width: 180px;">
      <?php
        $cur = (int)($_GET['days'] ?? 14);
        $opts = [7, 14, 30, 90, 180];
      ?>
      <?php foreach ($opts as $d): ?>
        <option value="<?php echo $d; ?>" <?php echo ($cur === $d) ? 'selected' : ''; ?>>
          Últimos <?php echo $d; ?> días
        </option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn-outline-light">Aplicar</button>
  </form>
</div>

<div class="row g-3 mb-3">
  <div class="col-md-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="text-muted">Pedidos</div>
        <div class="display-6 fw-bold"><?php echo (int)$kpis['orders_count']; ?></div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="text-muted">Facturación (sin IVA)</div>
        <div class="display-6 fw-bold"><?php echo number_format((float)$kpis['net_total'], 2, ',', '.'); ?>€</div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="text-muted">Ticket medio</div>
        <div class="display-6 fw-bold"><?php echo number_format((float)$kpis['avg_ticket'], 2, ',', '.'); ?>€</div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-md-8">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="mb-3">Ventas por día</h5>
        <canvas id="chartByDay" height="110"></canvas>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="mb-3">Ventas por categoría</h5>
        <canvas id="chartByCategory" height="180"></canvas>
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm mt-3">
  <div class="card-body">
    <h5 class="mb-3">Top productos</h5>

    <?php if (empty($topProducts)): ?>
      <div class="alert alert-info mb-0">Aún no hay ventas en este rango.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-dark">
            <tr>
              <th>Producto</th>
              <th class="text-end">Unidades</th>
              <th class="text-end">Importe (sin IVA)</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($topProducts as $p): ?>
              <tr>
                <td><?php echo htmlspecialchars($p['name']); ?></td>
                <td class="text-end fw-bold"><?php echo (int)$p['units']; ?></td>
                <td class="text-end"><?php echo number_format((float)$p['revenue'], 2, ',', '.'); ?>€</td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
  // Datos PHP -> JS
  const byDayLabels = <?php echo json_encode(array_map(fn($r) => $r['day'], $byDay)); ?>;
  const byDayValues = <?php echo json_encode(array_map(fn($r) => (float)$r['revenue'], $byDay)); ?>;

  const byCatLabels = <?php echo json_encode(array_map(fn($r) => $r['category'], $byCategory)); ?>;
  const byCatValues = <?php echo json_encode(array_map(fn($r) => (float)$r['revenue'], $byCategory)); ?>;

  const ctx1 = document.getElementById('chartByDay');
  if (ctx1) {
    new Chart(ctx1, {
      type: 'line',
      data: {
        labels: byDayLabels,
        datasets: [{
          label: 'Ventas (€)',
          data: byDayValues,
          tension: 0.25
        }]
      }
    });
  }

  const ctx2 = document.getElementById('chartByCategory');
  if (ctx2) {
    new Chart(ctx2, {
      type: 'doughnut',
      data: {
        labels: byCatLabels,
        datasets: [{
          label: '€',
          data: byCatValues
        }]
      }
    });
  }
</script>
