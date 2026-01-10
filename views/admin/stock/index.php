<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="mb-0">📦 Stock</h2>

  <form class="d-flex" method="GET" action="index.php">
    <input type="hidden" name="c" value="stock">
    <input type="hidden" name="a" value="index">
    <input class="form-control me-2" name="q" placeholder="Buscar producto o categoría..."
           value="<?php echo htmlspecialchars($q ?? ''); ?>">
    <button class="btn btn-outline-light">Buscar</button>
  </form>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-dark">
        <tr>
          <th>Producto</th>
          <th>Categoría</th>
          <th class="text-end">Stock</th>
          <th class="text-end">Min</th>
          <th class="text-end">Max</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($rows as $r): ?>
          <?php
            $qty = (int)$r['cantidad'];
            $min = $r['cantidad_min'] !== null ? (int)$r['cantidad_min'] : null;
            $max = $r['cantidad_max'] !== null ? (int)$r['cantidad_max'] : null;

            $low = ($min !== null && $qty <= $min);
          ?>
          <tr class="<?php echo $low ? 'table-warning' : ''; ?>">
            <td>
              <strong><?php echo htmlspecialchars($r['product_name']); ?></strong>
              <?php if ((int)$r['is_active'] !== 1): ?>
                <span class="badge bg-secondary ms-2">Inactivo</span>
              <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($r['category_name']); ?></td>
            <td class="text-end fw-bold"><?php echo $qty; ?></td>
            <td class="text-end"><?php echo $min !== null ? $min : '-'; ?></td>
            <td class="text-end"><?php echo $max !== null ? $max : '-'; ?></td>

            <td class="text-end">
              <!-- IN -->
              <form class="d-inline" method="POST" action="index.php?c=stock&a=move">
                <input type="hidden" name="product_id" value="<?php echo (int)$r['product_id']; ?>">
                <input type="hidden" name="type" value="IN">
                <input type="number" name="qty" min="1" value="1" class="form-control d-inline-block"
                       style="width:90px;">
                <button class="btn btn-sm btn-success">+ IN</button>
              </form>

              <!-- OUT -->
              <form class="d-inline" method="POST" action="index.php?c=stock&a=move">
                <input type="hidden" name="product_id" value="<?php echo (int)$r['product_id']; ?>">
                <input type="hidden" name="type" value="OUT">
                <input type="number" name="qty" min="1" value="1" class="form-control d-inline-block"
                       style="width:90px;">
                <button class="btn btn-sm btn-danger">- OUT</button>
              </form>

              <!-- SET -->
              <form class="d-inline" method="POST" action="index.php?c=stock&a=set">
                <input type="hidden" name="product_id" value="<?php echo (int)$r['product_id']; ?>">
                <input type="number" name="qty" min="0" value="<?php echo $qty; ?>"
                       class="form-control d-inline-block" style="width:110px;">
                <button class="btn btn-sm btn-outline-primary">Set</button>
              </form>

              <a class="btn btn-sm btn-outline-secondary"
                 href="index.php?c=stock&a=history&product_id=<?php echo (int)$r['product_id']; ?>">
                Historial
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="alert alert-info mt-3 mb-0">
  “Set” guarda un movimiento <strong>ADJUST</strong> con la diferencia. “IN/OUT” guarda movimiento normal.
</div>
