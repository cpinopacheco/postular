<?php

require_once 'config/load_env.php';
require_once 'config/db.php';
require_once 'app/controllers/PostulacionController.php';

$controller = new PostulacionController();

// Basic Router
$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'validar':
        $controller->validar();
        break;
    case 'inscribir':
        $controller->inscribir();
        break;
    case 'certificado':
        // TODO: Implementar método para ver certificado por URL si es necesario
        echo "Vista de certificado pendiente implementation directa";
        break;
    default:
        $controller->index();
        break;
}
