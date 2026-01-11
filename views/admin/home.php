<div class="p-5 mb-4 bg-light border rounded shadow-sm text-center">
  <h1 class="display-5 fw-bold">Panel de Administración</h1>
  <p class="lead mb-1">El Punto Ciego · Gestión interna</p>
  
  <p class="text-muted mb-0">
    Hola, <i class="fa-solid fa-user-tie"></i><strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></strong>.
    Desde aquí puedes gestionar usuarios, productos, categorías, packs, stock y consultar ventas.
  </p>
</div>

<h3 class="mb-3">Accesos rápidos</h3>

<div class="row">
  <div class="col-md-4 mb-3">
    <a class="text-decoration-none" href="index.php?c=user&a=index">
      <div class="card shadow-sm h-100">
        <div class="card-body">
            <div class="d-flex align-items-center mb-2">
                <i class="fa-solid fa-users fa-lg text-primary me-2"></i>
                <h5 class="card-title mb-0">Usuarios</h5>
            </div>
          <p class="text-muted mb-0">Ver usuarios, activar/desactivar, cambiar roles.</p>
        </div>
      </div>
    </a>
  </div>

  <div class="col-md-4 mb-3">
    <a class="text-decoration-none" href="index.php?c=admin_product&a=index">
      <div class="card shadow-sm h-100">
        <div class="card-body">
            <div class="d-flex align-items-center mb-2">
                <i class="fa-solid fa-box fa-lg text-success me-2"></i>
                <h5 class="card-title mb-0">Productos</h5>
            </div>
          <p class="text-muted mb-0">CRUD de productos, precios, categorías y proveedor.</p>
        </div>
      </div>
    </a>
  </div>

  <div class="col-md-4 mb-3">
    <a class="text-decoration-none" href="index.php?c=admin_category&a=index">
      <div class="card shadow-sm h-100">
        <div class="card-body">
            <div class="d-flex align-items-center mb-2">
                <i class="fa-solid fa-tags fa-lg text-warning me-2"></i>
                <h5 class="card-title mb-0">Categorías</h5>
            </div>
            <p class="text-muted mb-0">Crear/editar categorías y asignar IVA.</p>
        </div>
      </div>
    </a>
  </div>

  <div class="col-md-4 mb-3">
    <a class="text-decoration-none" href="index.php?c=admin_pack&a=index">
      <div class="card shadow-sm h-100">
        <div class="card-body">
            <div class="d-flex align-items-center mb-2">
                <i class="fa-solid fa-boxes-stacked fa-lg text-info me-2"></i>
                <h5 class="card-title mb-0">Packs</h5>
            </div>
            <p class="text-muted mb-0">CRUD de packs y productos incluidos.</p>
        </div>
      </div>
    </a>
  </div>

  <div class="col-md-4 mb-3">
    <a class="text-decoration-none" href="index.php?c=stock&a=index">
      <div class="card shadow-sm h-100">
        <div class="card-body">
            <div class="d-flex align-items-center mb-2">
                <i class="fa-solid fa-warehouse fa-lg text-danger me-2"></i>
                <h5 class="card-title mb-0">Stock</h5>
            </div>
            <p class="text-muted mb-0">Ajustes, entradas/salidas y movimientos.</p>
        </div>
      </div>
    </a>
  </div>

  <div class="col-md-4 mb-3">
    <a class="text-decoration-none" href="index.php?c=admin_analytics&a=index">
      <div class="card shadow-sm h-100">
        <div class="card-body">
            <div class="d-flex align-items-center mb-2">
                <i class="fa-solid fa-chart-line fa-lg text-danger me-2"></i>
                <h5 class="card-title mb-0">Análisis</h5>
            </div>
            <p class="text-muted mb-0">Ventas, top productos, ingresos, etc.</p>
        </div>
      </div>
    </a>
  </div>
</div>

<!-- <div class="alert alert-warning mt-4">
  <strong>Nota:</strong> Algunos accesos todavía pueden estar “en construcción”. Iremos implementándolos uno por uno.
</div> -->
