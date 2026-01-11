<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>El Punto Ciego - Asociación</title>
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/proyecto_drogas/public/css/styles.css?v=2">

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    crossorigin="anonymous"
    referrerpolicy="no-referrer">



</head>
<body class="bg-light">
    <?php include 'views/partials/navbar.php'; ?>

    <div class="container mt-4">
        
        <?php
        // Construimos un "toast" desde msg/error/flash
        $toast = null;

        // Prioridad: error > msg > flash
        if (isset($_GET['error']) && $_GET['error'] !== '') {
            $toast = ['type' => 'danger', 'title' => 'Error', 'text' => $_GET['error']];
        } elseif (isset($_GET['msg']) && $_GET['msg'] !== '') {
            $toast = ['type' => 'success', 'title' => 'Éxito', 'text' => $_GET['msg']];
        } elseif (!empty($_SESSION['flash_msg'])) {
            $toast = ['type' => 'success', 'title' => 'OK', 'text' => $_SESSION['flash_msg']];
            unset($_SESSION['flash_msg']);
        }
        ?>

        <?php if ($toast): ?>
        <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
            <div id="appToast"
                class="toast align-items-center text-bg-<?php echo htmlspecialchars($toast['type']); ?> border-0"
                role="alert" aria-live="assertive" aria-atomic="true"
                data-bs-delay="2500">
            <div class="d-flex">
                <div class="toast-body">
                <strong class="me-2"><?php echo htmlspecialchars($toast['title']); ?>:</strong>
                <?php echo htmlspecialchars($toast['text']); ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            </div>
        </div>
        <?php endif; ?>


        <!-- Contenido Dinámico -->
        <div class="main-content">
            <?php echo $viewContent; ?>
        </div>

    </div>
    <br><br><br>

    <?php include 'views/partials/footer.php'; ?>

    <!-- JS de Bootstrap (necesario para cerrar las alertas y el menú) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('appToast');
        if (el && window.bootstrap) {
        const toast = new bootstrap.Toast(el);
        toast.show();
        }
    });
    </script>

    <script src="public/js/catalog.js"></script>
</body>
</html>