<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="mb-0">📦 Packs</h2>
  <a class="btn btn-success" href="index.php?c=admin_pack&a=create"><i class="fa-solid fa-plus me-2"></i>Nuevo pack</a>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Precio fijo</th>
          <th>Descuento %</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($packs as $p): ?>
          <tr>
            <td><?php echo (int)$p['id']; ?></td>
            <td><strong><?php echo htmlspecialchars($p['name']); ?></strong></td>
            <td><?php echo $p['final_price'] !== null ? number_format((float)$p['final_price'], 2) . '€' : '-'; ?></td>
            <td><?php echo $p['discount_percent'] !== null ? number_format((float)$p['discount_percent'], 2) . '%' : '-'; ?></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-success"
                 href="index.php?c=admin_pack&a=items&id=<?php echo (int)$p['id']; ?>">
                Contenido
              </a>
              <a class="btn btn-sm btn-outline-primary"
                 href="index.php?c=admin_pack&a=edit&id=<?php echo (int)$p['id']; ?>">
                <i class="fa-solid fa-pen-to-square me-2"></i>Editar
              </a>
              <form class="d-inline" method="POST" action="index.php?c=admin_pack&a=delete"
                    onsubmit="return confirm('¿Eliminar pack?');">
                <input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
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
  El precio del pack se calcula por suma de productos. Si indicas “precio fijo”, manda. Si no, aplica el descuento si existe.
</div>
