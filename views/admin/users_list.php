<h2 class="mb-4" style="color: mediumseagreen;"><i class="fa-solid fa-users me-2"></i> Gestión de usuarios</h2>

<div class="card shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Activo</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
            <tr>
              <td><?php echo (int)$u['id']; ?></td>
              <td><?php echo htmlspecialchars($u['name']); ?></td>
              <td><?php echo htmlspecialchars($u['gmail']); ?></td>
              <td><span class="badge bg-<?php echo ($u['role']==='admin') ? 'warning text-dark' : 'secondary'; ?>">
                <?php echo htmlspecialchars($u['role']); ?>
              </span></td>
              <td>
                <?php if ((int)$u['is_active'] === 1): ?>
                  <span class="badge bg-success">Sí</span>
                <?php else: ?>
                  <span class="badge bg-danger">No</span>
                <?php endif; ?>
              </td>
              <td class="text-end">
                <!-- Cambiar rol -->
                <form class="d-inline" method="POST" action="index.php?c=user&a=setRole">
                  <input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
                  <select name="role" class="form-select form-select-sm d-inline-block" style="width:auto;">
                    <option value="customer" <?php echo ($u['role']==='customer')?'selected':''; ?>>customer</option>
                    <option value="admin" <?php echo ($u['role']==='admin')?'selected':''; ?>>admin</option>
                  </select>
                  <button class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-floppy-disk me-2"></i>Guardar</button>
                </form>

                <!-- Activar/Desactivar -->
                <form class="d-inline ms-2" method="POST" action="index.php?c=user&a=toggleActive">
                  <input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
                  <input type="hidden" name="active" value="<?php echo ((int)$u['is_active'] === 1) ? 0 : 1; ?>">
                  <button class="btn btn-sm btn-outline-<?php echo ((int)$u['is_active'] === 1) ? 'danger' : 'success'; ?>">
                    <?php echo ((int)$u['is_active'] === 1) ? 'Desactivar' : 'Activar'; ?>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
