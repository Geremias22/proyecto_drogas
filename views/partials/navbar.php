<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="index.php"><i class="fa-solid fa-cannabis text-success"></i> El Punto Ciego</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">

      <!-- Lado Izquierdo -->
      <ul class="navbar-nav me-auto">
        <?php $isLogged = isset($_SESSION['user_id']); ?>
        <?php $isAdmin  = $isLogged && (($_SESSION['user_role'] ?? '') === 'admin'); ?>

        <?php if ($isAdmin): ?>
          <!-- Menú Admin -->
          <!-- <li class="nav-item">
            <a class="nav-link text-warning fw-bold" href="index.php">Panel Admin</a>
          </li> -->
<!-- 
          <li class="nav-item"><a class="nav-link" href="index.php?c=user&a=index">Usuarios</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?c=admin_product&a=index">Productos</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?c=admin_category&a=index">Categorías</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?c=admin_pack&a=index">Packs</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?c=stock&a=index">Stock</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?c=admin_analytics&a=index">Análisis</a></li>

          <li class="nav-item">
            <a class="nav-link text-info" href="index.php?c=product_list&a=index">Ver tienda</a>
          </li> -->

          <li class="nav-item"><a class="nav-link" href="index.php?c=user&a=index"><i class="fa-solid fa-users me-2"></i>Usuarios</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?c=admin_product&a=index"><i class="fa-solid fa-box me-2"></i>Productos</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?c=admin_category&a=index"><i class="fa-solid fa-tags me-2"></i>Categorías</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?c=admin_pack&a=index"><i class="fa-solid fa-boxes-stacked me-2"></i>Packs</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?c=stock&a=index"><i class="fa-solid fa-warehouse me-2"></i>Stock</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?c=admin_analytics&a=index"><i class="fa-solid fa-chart-line me-2"></i>Análisis</a></li>

          <!-- (Opcional) acceso a tienda para admin -->
          <li class="nav-item">
            <a class="nav-link text-info" href="index.php?c=product_list&a=index"><i class="fa-solid fa-shop me-2"></i>Ver tienda</a>
          </li>

        <?php else: ?>
          <!-- Menú Cliente / Invitado -->
          <li class="nav-item"><a class="nav-link" href="index.php?c=product_list&a=index">Catálogo</a></li>
        <?php endif; ?>
      </ul>

      <!-- Lado Derecho -->
      <ul class="navbar-nav ms-auto align-items-center">

        <?php if ($isLogged): ?>
          <li class="nav-item">
            <a class="nav-link text-light me-2" href="index.php?c=user&a=profile">
              Hola, <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></strong>
            </a>
          </li>

          <?php if (!$isAdmin): ?>
            <li class="nav-item">
              <a class="nav-link" href="index.php?c=order&a=my">Mis pedidos</a>
            </li>
          <?php endif; ?>

          <li class="nav-item border-end pe-2 me-2">
            <a class="nav-link btn btn-outline-secondary btn-sm text-white border-0"
               href="index.php?c=auth&a=logout">
              Cerrar Sesión
            </a>
          </li>

        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="index.php?c=auth&a=index&mode=login">Login</a>
          </li>
        <?php endif; ?>

        <!-- Carrito solo para NO admin -->
        <?php if (!$isAdmin): ?>
          <li class="nav-item">
            <a class="nav-link btn btn-success btn-sm text-white ms-lg-2 px-3" href="index.php?c=cart&a=index">
              <i class="fa-solid fa-cart-shopping"></i> Carrito
              <span class="badge bg-danger ms-1" data-cart-count>
                <?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>
              </span>
            </a>
          </li>
        <?php endif; ?>

      </ul>
    </div>
  </div>
</nav>
