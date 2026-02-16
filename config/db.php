<?php

class Database
{
    private $conn;

    public function __construct()
    {
        $this->loadEnv();
    }

    private function loadEnv()
    {
        if (!file_exists(__DIR__ . '/../.env')) {
            throw new Exception('.env file not found');
        }

        $env = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($env as $line) {
            $line = trim($line);
            // Omitir comentarios
            if (strpos($line, '#') === 0) {
                continue;
            }

            // Validar formato de línea
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }
    }

    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "pgsql:host=" . $_ENV['DB_HOST'] . ";port=" . ($_ENV['DB_PORT'] ?? 5432) . ";dbname=" . $_ENV['DB_NAME'],
                $_ENV['DB_USERNAME'],
                $_ENV['DB_PASSWORD']
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            // No mostrar errores en producción
            error_log("Error de conexión: " . $exception->getMessage());
            echo "Error de conexión. Intente más tarde.";
        }

        return $this->conn;
    }
}
