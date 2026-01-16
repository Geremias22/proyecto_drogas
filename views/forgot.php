<div class="row justify-content-center">
  <div class="col-md-5">
    <h2 class="text-center mb-3">Recuperar contraseña</h2>

    <form action="index.php?c=auth&a=forgotPost" method="POST" class="card p-4 shadow">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(SecurityHelper::generateCsrfToken()); ?>">
      
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="gmail" class="form-control" required>
        <div class="form-text">Te mostraremos tu pregunta de seguridad.</div>
      </div>

      <button type="submit" class="btn btn-primary w-100">Continuar</button>

      <div class="text-center mt-3">
        <a href="index.php?c=auth&a=index&mode=login">Volver al login</a>
      </div>
    </form>
  </div>
</div>
