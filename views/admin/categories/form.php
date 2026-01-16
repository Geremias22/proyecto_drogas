<?php
$isEdit = ($mode ?? '') === 'edit';
$action = $isEdit ? 'update' : 'store';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="mb-0"><?php echo $isEdit ? '✏️ Editar categoría' : '➕ Nueva categoría'; ?></h2>
  <a class="btn btn-outline-secondary" href="index.php?c=admin_category&a=index"><i class="fa-solid fa-arrow-left me-2"></i>Volver</a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="POST" action="index.php?c=admin_category&a=<?php echo $action; ?>">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(SecurityHelper::generateCsrfToken()); ?>">
      <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?php echo (int)$category['id']; ?>">
      <?php endif; ?>

      <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="name" class="form-control" required
               value="<?php echo htmlspecialchars($category['name'] ?? ''); ?>">
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Categoría padre (opcional)</label>
          <select name="parent_id" class="form-select">
            <option value="">-- Ninguna (principal) --</option>
            <?php foreach ($parents as $p): ?>
              <option value="<?php echo (int)$p['id']; ?>"
                <?php echo ((int)($category['parent_id'] ?? 0) === (int)$p['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($p['name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">IVA (opcional)</label>
          <select name="tax_id" class="form-select">
            <option value="">-- Sin asignar --</option>
            <?php foreach ($taxes as $t): ?>
              <option value="<?php echo (int)$t['id']; ?>"
                <?php echo ((int)($category['tax_id'] ?? 0) === (int)$t['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($t['name']); ?> (<?php echo number_format((float)$t['rate_iva'], 2); ?>%)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Imagen (opcional, ruta/URL)</label>
        <input type="text" name="image" class="form-control"
               value="<?php echo htmlspecialchars($category['image'] ?? ''); ?>">
      </div>

      <?php if ($isEdit): ?>
        <button class="btn btn-success"><i class="fa-solid fa-floppy-disk me-2"></i>Guardar cambios</button>
      <?php else: ?>
        <button class="btn btn-success"><i class="fa-solid fa-plus me-2"></i>Crear categoría</button>
      <?php endif; ?>
    </form>
  </div>
</div>
