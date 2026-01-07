<div class="row justify-content-center">
    <div class="col-md-4">
        <h2 class="text-center">Iniciar Sesión</h2>
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
            
            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger mt-3">Credenciales incorrectas</div>
            <?php endif; ?>
        </form>
    </div>
</div>