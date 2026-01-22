<?php
require_once 'config/db.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn) {
        echo "✅ ¡CONEXIÓN EXITOSA! La base de datos está conectada correctamente.\n";
    } else {
        echo "❌ Error: La conexión devolvió null.\n";
    }
} catch (Exception $e) {
    echo "❌ Error de excepción: " . $e->getMessage() . "\n";
}
