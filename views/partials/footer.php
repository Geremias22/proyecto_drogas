<?php
  // Puedes definir esto en config.php si quieres.
  $appName = 'El Punto Ciego';
  $appVersion = 'v1.1.0';
  $year = date('Y');

  // Edita nombres si quieres
  $creators = [
    'Geremias',
    'Equipo DAM/DAW'
  ];
?>
<footer class="pc-footer mt-5">
  <div class="container py-4">
    <div class="row g-4 align-items-start">

      <!-- Brand + descripcion -->
      <div class="col-md-5">
        <div class="d-flex align-items-center gap-2 mb-2">
          <span class="pc-footer-logo"><i class="fa-solid fa-cannabis text-success"></i></span>
          <span class="pc-footer-title"><?php echo htmlspecialchars($appName); ?></span>
          <span class="pc-footer-badge"><?php echo htmlspecialchars($appVersion); ?></span>
        </div>
        <p class="pc-footer-text mb-2">
          Tienda online desarrollada con PHP + MySQL siguiendo MVC, con mejoras de UX mediante AJAX.
        </p>
        <div class="pc-footer-mini">
          <span class="me-2"><i class="fa-solid fa-shield-halved"></i> Seguridad básica</span>
          <span class="me-2"><i class="fa-solid fa-bolt"></i> Experiencia rápida</span>
          <span><i class="fa-solid fa-leaf"></i> Estilo verde</span>
        </div>
      </div>

      <!-- Links -->
      <div class="col-6 col-md-3">
        <div class="pc-footer-head">Enlaces</div>
        <ul class="pc-footer-list">
          <li><a href="index.php?c=home&a=index">Inicio</a></li>
          <li><a href="index.php?c=product_list&a=index">Catálogo</a></li>
          <li><a href="index.php?c=cart&a=index">Carrito</a></li>
          <li><a href="index.php?c=auth&a=index&mode=login">Login</a></li>
        </ul>
      </div>

      <!-- Soporte / info -->
      <div class="col-6 col-md-4">
        <div class="pc-footer-head">Proyecto</div>
        <ul class="pc-footer-list">
          <li>
            <span class="pc-footer-label">Creadores:</span>
            <span><?php echo htmlspecialchars(implode(' · ', $creators)); ?></span>
          </li>
          <li>
            <span class="pc-footer-label">Stack:</span>
            <span>PHP · MySQL · Bootstrap · AJAX</span>
          </li>
          <li>
            <span class="pc-footer-label">Contacto:</span>
            <a href="mailto:soporte@elpuntociego.local">soporte@elpuntociego.local</a>
          </li>
        </ul>

        <div class="pc-footer-social mt-3">
          <a class="pc-icon-btn" href="#" title="Instagram" aria-label="Instagram">
            <i class="fa-brands fa-instagram"></i>
          </a>
          <a class="pc-icon-btn" href="#" title="GitHub" aria-label="GitHub">
            <i class="fa-brands fa-github"></i>
          </a>
          <a class="pc-icon-btn" href="#" title="X" aria-label="X">
            <i class="fa-brands fa-x-twitter"></i>
          </a>
        </div>
      </div>
    </div>

    <hr class="pc-footer-hr my-4">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
      <div class="pc-footer-copy">
        © <?php echo (int)$year; ?> <?php echo htmlspecialchars($appName); ?> · <?php echo htmlspecialchars($appVersion); ?>
      </div>

      <div class="pc-footer-legal">
        <a href="#">Privacidad</a>
        <span class="pc-footer-dot">·</span>
        <a href="#">Términos</a>
        <span class="pc-footer-dot">·</span>
        <a href="#">Cookies</a>
      </div>
    </div>
  </div>
</footer>
