<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="mb-0">📦 Productos</h2>
  <a class="btn btn-success" href="index.php?c=admin_product&a=create">+ Nuevo producto</a>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Categoría</th>
          <th>Proveedor</th>
          <th class="text-end">Precio</th>
          <th class="text-center">Stock</th>
          <th class="text-center">Activo</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
          <tr>
            <td><?php echo (int)$p['id']; ?></td>
            <td><strong><?php echo htmlspecialchars($p['name']); ?></strong></td>
            <td><?php echo htmlspecialchars($p['category_name']); ?></td>
            <td><?php echo htmlspecialchars($p['supplier_name'] ?? '-'); ?></td>
            <td class="text-end"><?php echo number_format((float)$p['price'], 2); ?>€</td>
            <td class="text-center"><?php echo (int)($p['stock_qty'] ?? 0); ?></td>
            <td class="text-center">
              <span class="badge bg-<?php echo ((int)$p['is_active']===1) ? 'success' : 'secondary'; ?>">
                <?php echo ((int)$p['is_active']===1) ? 'Sí' : 'No'; ?>
              </span>
            </td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="index.php?c=admin_product&a=edit&id=<?php echo (int)$p['id']; ?>">
                Editar
              </a>

              <form class="d-inline" method="POST" action="index.php?c=admin_product&a=delete"
                    onsubmit="return confirm('¿Desactivar este producto?');">
                <input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
                <button class="btn btn-sm btn-outline-danger">Desactivar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
