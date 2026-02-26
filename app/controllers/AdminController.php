<?php

require_once 'app/models/Postulante.php';
require_once 'app/models/Proceso.php';
require_once 'config/db.php';

class AdminController
{
    private $db;
    private $procesoModel;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->procesoModel = new Proceso($this->db);
    }

    private function checkAuth()
    {
        if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
            header('Location: /index.php?action=admin_login');
            exit;
        }
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            $adminPass = getenv('ADMIN_PASSWORD') ?: 'admin123';

            if ($password === $adminPass) {
                $_SESSION['admin_logged'] = true;
                header('Location: /index.php?action=admin');
                exit;
            } else {
                $error = "Contraseña incorrecta.";
                require 'app/views/admin/login.php';
                exit;
            }
        }
        require 'app/views/admin/login.php';
    }

    public function logout()
    {
        unset($_SESSION['admin_logged']);
        header('Location: /index.php?action=admin_login');
        exit;
    }

    public function index()
    {
        $this->checkAuth();
        
        // Estadísticas: Inscritos por grado
        $stmt = $this->db->prepare("
            SELECT i.\"GRADO\" as grado, COUNT(*) as total 
            FROM inscritos i 
            GROUP BY i.\"GRADO\" 
            ORDER BY total DESC
        ");
        $stmt->execute();
        $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Total inscritos
        $totalInscritos = array_sum(array_column($stats, 'total'));

        // Inscritos hoy (últimas 24 horas)
        $stmtHoy = $this->db->prepare("SELECT COUNT(*) FROM inscritos WHERE \"FECH_INSC\" >= CURRENT_DATE");
        $stmtHoy->execute();
        $inscritosHoy = $stmtHoy->fetchColumn();

        // Días restantes
        $procesoFin = $this->procesoModel->getFechaFin();
        $diferencia = strtotime($procesoFin) - time();
        $diasRestantes = ($diferencia > 0) ? ceil($diferencia / (60 * 60 * 24)) : 0;

        // Rechazos recientes (Exclusiones)
        $stmtExcl = $this->db->prepare("SELECT * FROM exclusiones ORDER BY fecha DESC LIMIT 5");
        $stmtExcl->execute();
        $recentExclusions = $stmtExcl->fetchAll(PDO::FETCH_ASSOC);

        require 'app/views/admin/dashboard.php';
    }

    public function config()
    {
        // Obtener configuración actual
        $stmt = $this->db->prepare("SELECT clave, valor FROM configuracion");
        $stmt->execute();
        $configRaw = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // Lista estandarizada de grados (Basada en .env y NormalizationHelper)
        $availableGrades = [
            'PNS' => [
                'CAPITAN', 'CAPITAN I', 'TENIENTE', 'TTE. I', 
                'TTE. CORONEL', 'TTE. CORONEL I', 'TTE. CORONEL SERV.'
            ],
            'PNI' => [
                'Orden y Seguridad' => [
                    'CARABINERO', 'CABO 2DO.', 'SGTO. 2DO.'
                ],
                'Servicios' => [
                    'CARAB. SERVICIOS', 'CABO 2DO. SERVICIOS', 'SGTO. 2DO. SERVICIOS'
                ],
                'Secretaría' => [
                    'CABO 2DO. SEC.', 'CABO 1RO. SEC.', 'SGTO. 2DO. SEC.'
                ],
                'Suboficiales' => [
                    'SUBOFICIAL', 'SUBOF.'
                ]
            ]
        ];

        require 'app/views/admin/settings.php';
    }

    public function saveConfig()
    {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Convertir array de grados a string separado por coma
            $gradosArray = $_POST['grados_habilitados'] ?? [];
            $gradosString = is_array($gradosArray) ? implode(',', $gradosArray) : '';

            $updates = [
                'PROCESO_FECHA_INICIO' => $_POST['proceso_inicio'] ?? '',
                'PROCESO_FECHA_FIN' => $_POST['proceso_fin'] ?? '',
                'PROCESO_GRADOS_HABILITADOS' => $gradosString
            ];

            $stmt = $this->db->prepare("UPDATE configuracion SET valor = :valor WHERE clave = :clave");
            
            foreach ($updates as $clave => $valor) {
                $stmt->execute([':valor' => $valor, ':clave' => $clave]);
            }

            $_SESSION['success_msg'] = "Configuración actualizada correctamente.";
            header('Location: /index.php?action=admin_config');
            exit;
        }
    }
}
