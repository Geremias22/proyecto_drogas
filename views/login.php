<form action="index.php?c=auth&a=login" method="POST" class="card p-4 shadow">
    <div class="mb-3">
        <label>Email (Gmail)</label>
        <input type="email" name="gmail" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Contraseña</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary w-100">Entrar</button>
    <div class="text-center mt-3">
        <a href="index.php?c=auth&a=forgot">¿Has olvidado la contraseña?</a>
    </div>

    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger mt-3 mb-0">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>
</form>
