<div class="row justify-content-center">
    <div class="col-md-6">
        <h2 class="text-center mb-3">Restablecer contraseña</h2>

        <form action="index.php?c=auth&a=resetPost" method="POST" class="card p-4 shadow needs-validation" novalidate>
            <input type="hidden" name="uid" value="<?php echo (int)$uid; ?>">

            <div class="mb-3">
                <label class="form-label">Pregunta de seguridad</label>
                <div class="form-control bg-light"><?php echo htmlspecialchars($question); ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Tu respuesta</label>
                <input type="text" name="security_answer" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nueva contraseña</label>
                <input
                    type="password"
                    name="new_password"
                    class="form-control"
                    required
                    minlength="8"
                    maxlength="72"
                    pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,72}$">
                <div class="invalid-feedback">8+ caracteres, con mayúscula, minúscula, número y símbolo.</div>
            </div>


            <button type="submit" class="btn btn-success w-100">Cambiar contraseña</button>

            <div class="text-center mt-3">
                <a href="index.php?c=auth&a=forgot">Volver</a>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
  const form = document.querySelector('.needs-validation');
  if (!form) return;

  form.addEventListener('submit', function (event) {
    if (!form.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
    }
    form.classList.add('was-validated');
  }, false);
})();
</script>
