<?php
$isEdit = ($mode ?? '') === 'edit';
$action = $isEdit ? 'update' : 'store';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="mb-0"><?php echo $isEdit ? '✏️ Editar producto' : '➕ Nuevo producto'; ?></h2>
  <a class="btn btn-outline-secondary" href="index.php?c=admin_product&a=index"><i class="fa-solid fa-arrow-left me-2"></i>Volver</a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="POST" action="index.php?c=admin_product&a=<?php echo $action; ?>">
      <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?php echo (int)$product['id']; ?>">
      <?php endif; ?>

      <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="name" class="form-control" required
               value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Descripción</label>
        <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
      </div>

      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label">Precio</label>
          <input type="number" step="0.01" min="0" name="price" class="form-control" required
                 value="<?php echo htmlspecialchars($product['price'] ?? '0'); ?>">
        </div>

        <div class="col-md-4 mb-3">
          <label class="form-label">Categoría</label>
          <select name="category_id" class="form-select" required>
            <option value="">-- Selecciona --</option>
            <?php foreach ($categories as $c): ?>
              <option value="<?php echo (int)$c['id']; ?>"
                <?php echo ((int)($product['category_id'] ?? 0) === (int)$c['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($c['name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-4 mb-3">
          <label class="form-label">Proveedor (opcional)</label>
          <select name="supplier_id" class="form-select">
            <option value="">-- Ninguno --</option>
            <?php foreach ($suppliers as $s): ?>
              <option value="<?php echo (int)$s['id']; ?>"
                <?php echo ((int)($product['supplier_id'] ?? 0) === (int)$s['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($s['name']); ?> (<?php echo htmlspecialchars($s['code']); ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-check mb-3">
        <?php $checked = ((int)($product['is_active'] ?? 1) === 1) ? 'checked' : ''; ?>
        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?php echo $checked; ?>>
        <label class="form-check-label" for="is_active">Producto activo</label>
      </div>

      <?php if ($isEdit): ?>
        <button class="btn btn-success"><i class="fa-solid fa-floppy-disk me-2"></i>Guardar cambios</button>
      <?php else: ?>
        <button class="btn btn-success"><i class="fa-solid fa-plus me-2"></i>Crear producto</button>
      <?php endif; ?>
    </form>
  </div>
</div>
