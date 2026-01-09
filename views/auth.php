<?php
$mode = $mode ?? ($_GET['mode'] ?? 'login');
?>

<div class="row justify-content-center">
    <div class="col-12 col-sm-10 col-md-7 col-lg-6">

        <div class="text-center mb-4">
            <h2 class="mb-1">
                <?php echo ($mode === 'register') ? 'Crear Cuenta' : 'Iniciar Sesión'; ?>
            </h2>
            <p class="text-muted mb-0">
                <?php echo ($mode === 'register')
                    ? 'Únete a la asociación y gestiona tus pedidos.'
                    : 'Accede para finalizar tu compra y ver tus pedidos.'; ?>
            </p>
        </div>

        <?php if ($mode === 'register'): ?>
            <?php require 'views/register.php'; ?>
            <div class="text-center mt-3">
                <span class="text-muted">¿Ya tienes cuenta?</span>
                <a href="index.php?c=auth&a=index&mode=login">Inicia sesión</a>
            </div>
        <?php else: ?>
            <?php require 'views/login.php'; ?>
            <div class="text-center mt-3">
                <span class="text-muted">¿Aún no tienes cuenta?</span>
                <a href="index.php?c=auth&a=index&mode=register">Regístrate</a>
            </div>
        <?php endif; ?>

    </div>
</div>
