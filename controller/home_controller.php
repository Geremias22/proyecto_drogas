<?php
class HomeController {
    public function index() {
        $name = $_SESSION['user_name'] ?? 'Invitado';
        
        $viewContent = "
            <div class='jumbotron mt-4 p-5 bg-light border rounded shadow-sm text-center'>
                <h1 class='display-4'>¡Hola, $name!</h1>
                <p class='lead'>Bienvenido a la Asociación El Punto Ciego.</p>
                <hr class='my-4'>
                <p>Explora nuestro catálogo de productos y gestiona tus pedidos de forma segura.</p>
                <a class='btn btn-success btn-lg' href='index.php?c=product_list' role='button'>Ver Productos</a>
            </div>
        ";

        require_once 'views/layouts/main.php';
    }
}