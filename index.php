<?php
session_start();

require_once 'model/conectaDB.php';

$controllerName = $_GET['c'] ?? 'home'; // Controlador por defecto
$action = $_GET['a'] ?? 'index';        // Acción por defecto

$controllerFile = "controller/{$controllerName}_controller.php";

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    $className = str_replace('_', '', ucwords($controllerName, '_')) . 'Controller';
    
    if (class_exists($className)) {
        $controller = new $className();
        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            die("La acción $action no existe.");
        }
    } else {
        die("La clase $className no existe.");
    }
} else {
    die("El controlador $controllerName no existe.");
}