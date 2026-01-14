<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="mb-0" style="color: mediumseagreen;"><i class="fa-solid fa-tags me-2"></i> Categorías</h2>
  <a class="btn btn-success" href="index.php?c=admin_category&a=create"><i class="fa-solid fa-plus me-2"></i>Nueva categoría</a>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Padre</th>
          <th>IVA</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($categories as $c): ?>
          <tr>
            <td><?php echo (int)$c['id']; ?></td>
            <td><strong><?php echo htmlspecialchars($c['name']); ?></strong></td>
            <td><?php echo htmlspecialchars($c['parent_name'] ?? '-'); ?></td>
            <td>
              <?php if (!empty($c['tax_name'])): ?>
                <?php echo htmlspecialchars($c['tax_name']); ?> (<?php echo number_format((float)$c['tax_rate'], 2); ?>%)
              <?php else: ?>
                -
              <?php endif; ?>
            </td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary"
                 href="index.php?c=admin_category&a=edit&id=<?php echo (int)$c['id']; ?>">
                <i class="fa-solid fa-pen-to-square me-2"></i>Editar
              </a>

              <form class="d-inline" method="POST" action="index.php?c=admin_category&a=delete"
                    onsubmit="return confirm('¿Eliminar categoría?');">
                <input type="hidden" name="id" value="<?php echo (int)$c['id']; ?>">
                <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash-can me-2"></i>Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="alert alert-info mt-3 mb-0">
  Si una categoría tiene subcategorías o productos asignados, no se puede borrar (para no romper datos).
</div>
