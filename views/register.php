<form action="index.php?c=auth&a=registerPost" method="POST" class="card p-4 shadow">
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Email (Gmail)</label>
        <input type="email" name="gmail" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Edad (opcional)</label>
        <input type="number" name="edad" class="form-control" min="0" max="120">
    </div>

    <div class="mb-3">
        <label>Contraseña</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-success w-100">Registrarme</button>

    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger mt-3 mb-0">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>
</form>
