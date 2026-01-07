<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="index.php">🌿 El Punto Ciego</a>

    <div class="collapse navbar-collapse" id="navbarNav">
      <!-- Lado Izquierdo -->
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="index.php?c=product_list">Catálogo</a></li>

        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
          <li class="nav-item"><a class="nav-link text-warning fw-bold" href="index.php?c=admin_panel">PANEL CONTROL</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?c=stock">Stock</a></li>
        <?php endif; ?>
      </ul>

      <!-- Lado Derecho -->
      <ul class="navbar-nav ms-auto align-items-center">
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item">
            <span class="nav-link text-light me-2">Hola, <strong><?php echo $_SESSION['user_name']; ?></strong></span>
          </li>
          <li class="nav-item border-end pe-2 me-2">
            <a class="nav-link btn btn-outline-secondary btn-sm text-white border-0" href="index.php?c=auth&a=logout">Cerrar Sesión</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="index.php?c=auth&a=index">Entrar</a>
          </li>
        <?php endif; ?>

        <!-- Carrito -->
        <li class="nav-item">
          <a class="nav-link btn btn-success btn-sm text-white ms-lg-2 px-3" href="index.php?c=cart&a=index">
            🛒 Carrito
            <span class="badge bg-danger ms-1">
              <?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>
            </span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>