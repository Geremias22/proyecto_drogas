<h2 class="mb-4">👤 Mi perfil</h2>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title mb-3">Datos</h5>

        <div class="mb-2"><strong>Nombre:</strong> <?php echo htmlspecialchars($user['name'] ?? ''); ?></div>
        <div class="mb-2"><strong>Email:</strong> <?php echo htmlspecialchars($user['gmail'] ?? ''); ?></div>
        <div class="mb-2"><strong>Edad:</strong> <?php echo htmlspecialchars($user['edad'] ?? '-'); ?></div>
        <div class="mb-2"><strong>Rol:</strong> <?php echo htmlspecialchars($user['role'] ?? ''); ?></div>
        <div class="mb-0 text-muted small">
          Creado: <?php echo htmlspecialchars($user['date_create'] ?? ''); ?>
        </div>

        <hr>
        <a class="btn btn-outline-secondary w-100" href="index.php?c=auth&a=logout">Cerrar sesión</a>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title mb-3">🧾 Mis pedidos</h5>

        <?php if (empty($orders)): ?>
          <div class="alert alert-info mb-0">Aún no has realizado pedidos.</div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-dark">
                <tr>
                  <th>Referencia</th>
                  <th>Estado</th>
                  <th>Fecha</th>
                  <th class="text-end">Acción</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($orders as $o): ?>
                  <tr>
                    <td><strong><?php echo htmlspecialchars($o['order_reference']); ?></strong></td>
                    <td><?php echo htmlspecialchars($o['status']); ?></td>
                    <td><?php echo htmlspecialchars($o['created_at']); ?></td>
                    <td class="text-end">
                      <a class="btn btn-outline-primary btn-sm"
                         href="index.php?c=order&a=show&id=<?php echo (int)$o['id']; ?>">
                        Ver detalle
                      </a>
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
</div>
