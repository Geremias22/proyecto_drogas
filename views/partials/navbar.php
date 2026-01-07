<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="index.php">🌿 El Punto Ciego</a>
    
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="index.php?c=product_list">Catálogo</a></li>
        
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
          <!-- Enlaces SOLO para ADMIN -->
          <li class="nav-item"><a class="nav-link text-warning" href="index.php?c=admin_panel">PANEL CONTROL</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?c=stock">Gestionar Stock</a></li>
        <?php endif; ?>
      </ul>

      <ul class="navbar-nav">
        <?php if (isset($_SESSION['user_id'])): ?>
          <!-- Enlaces para usuarios LOGUEADOS -->
          <li class="nav-item">
            <span class="nav-link text-light">Hola, <strong><?php echo $_SESSION['user_name']; ?></strong></span>
          </li>
          <li class="nav-item"><a class="nav-link" href="index.php?c=auth&a=logout">Cerrar Sesión</a></li>
        <?php else: ?>
          <!-- Enlaces para GUESTS -->
          <li class="nav-item"><a class="nav-link" href="index.php?c=auth&a=index">Entrar</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>