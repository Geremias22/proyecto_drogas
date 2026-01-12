<?php
$sum = $final['sum'];
$finalPrice = $final['final'];
$mode = $final['mode'];
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h2 class="mb-0">📦 Contenido del pack: <?php echo htmlspecialchars($pack['name']); ?></h2>
    <div class="text-muted">
      Suma: <strong><?php echo number_format($sum, 2); ?>€</strong>
      · Final: <strong><?php echo number_format($finalPrice, 2); ?>€</strong>
      · Regla: <strong><?php echo htmlspecialchars($mode); ?></strong>
    </div>
  </div>

  <div>
    <a class="btn btn-outline-secondary" href="index.php?c=admin_pack&a=index"><i class="fa-solid fa-arrow-left me-2"></i>Volver</a>
    <a class="btn btn-outline-primary" href="index.php?c=admin_pack&a=edit&id=<?php echo (int)$pack['id']; ?>"><i class="fa-solid fa-pen-to-square me-2"></i>Editar pack</a>
  </div>
</div>

<div class="row">
  <div class="col-lg-7 mb-3">
    <div class="card shadow-sm">
      <div class="card-header fw-bold">Productos en el pack</div>
      <div class="card-body p-0">
        <?php if (empty($items)): ?>
          <div class="p-3">Este pack aún no tiene productos.</div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Producto</th>
                  <th>Categoría</th>
                  <th class="text-end">Precio</th>
                  <th class="text-end">Qty</th>
                  <th class="text-end">Subtotal</th>
                  <th class="text-end">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($items as $it): ?>
                  <tr>
                    <td><strong><?php echo htmlspecialchars($it['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($it['category_name']); ?></td>
                    <td class="text-end"><?php echo number_format((float)$it['price'], 2); ?>€</td>
                    <td class="text-end"><?php echo (int)$it['qty']; ?></td>
                    <td class="text-end">
                      <?php echo number_format(((float)$it['price']) * ((int)$it['qty']), 2); ?>€
                    </td>
                    <td class="text-end">
                      <form method="POST" action="index.php?c=admin_pack&a=itemRemove" class="d-inline">
                        <input type="hidden" name="pack_id" value="<?php echo (int)$pack['id']; ?>">
                        <input type="hidden" name="product_id" value="<?php echo (int)$it['product_id']; ?>">
                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Quitar del pack?');">
                          Quitar
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-lg-5 mb-3">
    <div class="card shadow-sm">
      <div class="card-header fw-bold">Añadir producto</div>
      <div class="card-body">
        <form method="GET" action="index.php" class="mb-3">
          <input type="hidden" name="c" value="admin_pack">
          <input type="hidden" name="a" value="items">
          <input type="hidden" name="id" value="<?php echo (int)$pack['id']; ?>">
          <label class="form-label">Buscar</label>
          <input type="text" name="q" class="form-control" value="<?php echo htmlspecialchars($q ?? ''); ?>" placeholder="Nombre o descripción...">
        </form>

        <form method="POST" action="index.php?c=admin_pack&a=itemAdd">
          <input type="hidden" name="pack_id" value="<?php echo (int)$pack['id']; ?>">

          <div class="mb-3">
            <label class="form-label">Producto</label>
            <select name="product_id" class="form-select" required>
              <option value="">-- Selecciona --</option>
              <?php foreach ($products as $p): ?>
                <option value="<?php echo (int)$p['id']; ?>">
                  <?php echo htmlspecialchars($p['name']); ?> · <?php echo number_format((float)$p['price'], 2); ?>€
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Cantidad</label>
            <input type="number" name="qty" min="1" value="1" class="form-control" required>
          </div>

          <button class="btn btn-success w-100"><i class="fa-solid fa-plus me-2"></i>Añadir / Actualizar</button>
        </form>

        <div class="alert alert-secondary mt-3 mb-0">
          Si el producto ya existe en el pack, se actualiza la cantidad.
        </div>
      </div>
    </div>
  </div>
</div>
