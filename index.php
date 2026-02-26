<?php
session_start();

require_once 'config/load_env.php';
require_once 'config/db.php';
require_once 'app/controllers/PostulacionController.php';
require_once 'app/controllers/AdminController.php';

$controller = new PostulacionController();
$adminController = new AdminController();

// Basic Router
$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'validar':
        $controller->validar();
        break;
    case 'inscribir':
        $controller->inscribir();
        break;
    // --- Rutas Admin ---
    case 'admin':
        $adminController->index();
        break;
    case 'admin_login':
        $adminController->login();
        break;
    case 'admin_config':
        $adminController->config();
        break;
    case 'admin_save':
        $adminController->saveConfig();
        break;
    case 'admin_logout':
        $adminController->logout();
        break;
    default:
        $controller->index();
        break;
}
