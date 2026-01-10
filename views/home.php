<div class="p-5 mb-4 bg-light border rounded shadow-sm text-center">
    <h1 class="display-4 fw-bold">El Punto Ciego</h1>
    <p class="lead mb-1">Asociación · Catálogo exclusivo para socios</p>
    <p class="text-muted mb-4">Hola, <strong><?php echo htmlspecialchars($name); ?></strong>. Explora productos, packs y material.</p>
    <a class="btn btn-success btn-lg" href="index.php?c=product_list">Ver catálogo</a>
</div>

    <?php
        $categoryIcons = [
        'Materia prima'        => 'fa-seedling text-success',
        'Productos preparados' => 'fa-joint text-warning',
        'Material fumador'     => 'fa-fire text-danger',
        'Merch'                => 'fa-shirt text-primary',
        ];
    ?>
<h3 class="mb-3">Categorías</h3>
<div class="row">
  <?php foreach ($categories as $c): ?>
    <?php
      $iconClass = $categoryIcons[$c['name']] ?? 'fa-tags text-secondary';
    ?>
    <div class="col-md-3 mb-3">
      <a class="text-decoration-none"
         href="index.php?c=product_list&a=index&category=<?php echo (int)$c['id']; ?>">

        <div class="card shadow-sm h-100">
          <div class="card-body d-flex align-items-center justify-content-center gap-2">
            <i class="fa-solid <?php echo $iconClass; ?> fa-lg"></i>
            <h5 class="mb-0"><?php echo htmlspecialchars($c['name']); ?></h5>
          </div>
        </div>

      </a>
    </div>
  <?php endforeach; ?>
</div>


<h3 class="mt-4 mb-3">Packs destacados</h3>
<div class="row">
    <?php if (empty($packs)): ?>
        <div class="col-12">
            <div class="alert alert-info">Aún no hay packs disponibles.</div>
        </div>
    <?php else: ?>
        <?php foreach ($packs as $p): ?>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($p['name']); ?></h5>
                        <p class="text-muted small mb-0">Pack recomendado para nuevos socios.</p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a class="btn btn-outline-success w-100" href="index.php?c=pack&a=show&id=<?php echo (int)$p['id']; ?>">
                            Ver pack
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
