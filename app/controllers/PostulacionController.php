<?php

require_once 'app/models/Postulante.php';
require_once 'app/models/Proceso.php';
require_once 'config/db.php';

class PostulacionController
{
    private $db;
    private $postulanteModel;
    private $procesoModel;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->postulanteModel = new Postulante($this->db);
        $this->procesoModel = new Proceso();
    }

    public function index()
    {
        // Verificar si el proceso está abierto antes de mostrar el login
        if (!$this->procesoModel->isAbierto()) {
            require 'app/views/fin_proceso.php';
            return;
        }

        require 'app/views/inicio.php';
    }

    public function validar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }

        $codigoNum = $_POST['codigo_num'] ?? '';
        $codigoDv = strtoupper($_POST['codigo_dv'] ?? '');
        $codigo = $codigoNum . $codigoDv; // Asumiendo formato concatenado sin guión
        
        // 1. ¿Proceso abierto?
        if (!$this->procesoModel->isAbierto()) {
            require 'app/views/fin_proceso.php';
            exit;
        }

        // 2. ¿Ya está inscrito?
        $inscrito = $this->postulanteModel->isInscrito($codigo);
        if ($inscrito) {
            // Pasar datos a la vista si es necesario
            $datosInscripcion = $inscrito;
            require 'app/views/certificado.php';
            exit;
        }

        // 3. ¿Existe en BD?
        $funcionario = $this->postulanteModel->findByCodigo($codigo);
        if (!$funcionario) {
            error_log("[DEBUG] ERROR: Funcionario no encontrado.");
            $this->rechazar(null, "ERROR: El código ingresado no existe en la base de datos de habilitados.");
            exit;
        }

        // 4. ¿Es reincorporado / recurso?
        // La columna REI_REC contiene "SI" o "NO"
        $esReincorporado = (strtoupper($funcionario['REI_REC'] ?? 'NO') === 'SI');
        
        if ($esReincorporado) {
            $this->mostrarFormulario($funcionario);
            exit;
        }
        
        // Si NO es reincorporado, continuar con validaciones de notas
        
        // LÓGICA DE VALIDACIÓN COMBINADA (Rama A: Sin Notas + Rama B: Logica Estricta Anterior)
        error_log("[DEBUG] >>> INICIO VALIDACIÓN para Funcionario: " . $codigo);
        
        // 1. Obtención del grado actual
        $gradoActual = $funcionario['GRADO'] ?? '';
        if (empty($gradoActual)) {
            error_log("[DEBUG] ERROR: Grado no determinado.");
            $this->rechazar($funcionario, "ERROR: No se pudo determinar el grado actual del funcionario.");
            exit;
        }
        error_log("[DEBUG] [Paso 1] Grado actual en POSTULANTES: '" . $gradoActual . "'");
        
        // 2. Selección de notas del grado actual
        $notas = $this->postulanteModel->getNotasByGrado($codigo, $gradoActual);
        
        // --- RAMA A: CASO SI NO TIENE NOTAS EN EL GRADO ACTUAL ---
        if (empty($notas)) {
            error_log("[DEBUG] [Rama A] No se encontraron notas para el grado '" . $gradoActual . "'. Evaluando antigüedad desde ascenso.");
            
            $fechaAscensoStr = $funcionario['FECH_ASC'] ?? '';
            if (empty($fechaAscensoStr)) {
                error_log("[DEBUG] [Rama A.1] No tiene FECH_ASC. Permitiendo inscripción por defecto.");
                $this->mostrarFormulario($funcionario);
                exit;
            }

            $fechaAscenso = new DateTime($fechaAscensoStr);
            $hoy = new DateTime();
            $diferencia = $hoy->diff($fechaAscenso);
            
            error_log("[DEBUG] [Rama A.2] Tiempo desde ascenso (" . $fechaAscensoStr . "): " . $diferencia->y . " años.");

            if ($diferencia->y > 2) {
                error_log("[DEBUG] !!! RECHAZO: Han pasado más de 2 años desde su ascenso sin registros de notas.");
                $this->rechazar($funcionario, "RECHAZO: Más de 2 años desde el ascenso sin registros de notas en grado actual.");
                exit;
            } else {
                error_log("[DEBUG] >>> APROBADO: Menos de 2 años desde el ascenso.");
                $this->mostrarFormulario($funcionario);
                exit;
            }
        }

        // --- RAMA B: CASO SI SÍ TIENE NOTAS (Lógica Anterior Verificada) ---
        error_log("[DEBUG] [Rama B] Se encontraron " . count($notas) . " registro(s) de notas. Evaluando normativa...");

        // 1. Regla de MÉRITO (Criterio de Exclusión: Ya realizó el curso)
        $tieneMerito = false;
        foreach ($notas as $nota) {
            if (strtoupper($nota['condicion'] ?? '') === 'MERITO') {
                $tieneMerito = true;
                error_log("[DEBUG] [Regla 1] MÉRITO DETECTADO: El funcionario ya cuenta con mérito en su grado actual (" . ($nota['ano'] ?? 'N/A') . ").");
                break;
            }
        }

        if ($tieneMerito) {
            error_log("[DEBUG] !!! RECHAZO: El funcionario ya aprobó/cursó con MÉRITO. No es necesario inscribirse.");
            $this->rechazar($funcionario, "RECHAZO: El funcionario ya posee MÉRITO en el grado actual.");
            exit;
        }

        // 2. Regla prioritaria: condición ANTIGÜEDAD (Identificación para saltar laguna)
        $tieneAntigüedad = false;
        foreach ($notas as $nota) {
            $condicionNota = strtoupper($nota['condicion'] ?? '');
            if ($condicionNota === 'ANTIGUEDAD' || $condicionNota === 'ANTIGÜEDAD') {
                $tieneAntigüedad = true;
                error_log("[DEBUG] [Regla 2] ANTIGÜEDAD DETECTADA: Identificada para prioridad sobre lagunas.");
                break;
            }
        }
        
        // 3. Límite máximo de repeticiones (3 consecutivas)
        error_log("[DEBUG] [Regla 3] Evaluando historial de reprobaciones consecutivas...");
        $reprobacionesConsecutivas = 0;
        $maxConsecutivas = 0;
        $ultimaReprobacionAnio = null;
        
        foreach ($notas as $nota) {
            $anio = (int)($nota['ano'] ?? 0);
            $condicion = strtoupper($nota['condicion'] ?? '');
            $esReprobado = ($condicion === 'REPROBADO');
            
            if ($esReprobado) {
                $reprobacionesConsecutivas++;
                $ultimaReprobacionAnio = $anio;
                if ($reprobacionesConsecutivas > $maxConsecutivas) {
                    $maxConsecutivas = $reprobacionesConsecutivas;
                }
                error_log("[DEBUG] -> Año " . $anio . ": REPROBADO. Contador: " . $reprobacionesConsecutivas);
            } else {
                $reprobacionesConsecutivas = 0;
            }
        }
        
        if ($maxConsecutivas >= 3) {
            error_log("[DEBUG] !!! RECHAZO CRÍTICO: 3 o más reprobaciones consecutivas.");
            $this->rechazar($funcionario, "RECHAZO: Límite alcanzado de 3 reprobaciones consecutivas en grado actual.");
            exit;
        }

        // Si no tiene 3 consecutivas, la antigüedad permite inscripción inmediata
        if ($tieneAntigüedad) {
            error_log("[DEBUG] >>> APROBADO: Prioridad otorgada por condición 'ANTIGÜEDAD'. Saltando validaciones de laguna.");
            $this->mostrarFormulario($funcionario);
            exit;
        }
        
        // 4. Validación de continuidad anual tras reprobación
        error_log("[DEBUG] [Regla 4] Verificando continuidad tras reprobación...");
        $tieneAlgunaReprobacion = ($ultimaReprobacionAnio !== null);
        
        if (!$tieneAlgunaReprobacion) {
            error_log("[DEBUG] >>> APROBADO: No registra reprobaciones en el grado actual.");
            $this->mostrarFormulario($funcionario);
            exit;
        } else {
            $anioProceso = (int)date('Y'); // 2026
            if ($anioProceso === ($ultimaReprobacionAnio + 1)) {
                error_log("[DEBUG] >>> APROBADO: Continuidad validada (postula al año siguiente de reprobar).");
                $this->mostrarFormulario($funcionario);
                exit;
            } else if ($anioProceso > ($ultimaReprobacionAnio + 1)) {
                error_log("[DEBUG] !!! RECHAZO: Laguna detectada tras reprobación en " . $ultimaReprobacionAnio);
                $this->rechazar($funcionario, "RECHAZO: Laguna detectada (No postuló el año consecutivo a su última reprobación).");
                exit;
            }
        }
        
        error_log("[DEBUG] >>> APROBADO: Flujo completado.");
        $this->mostrarFormulario($funcionario);
        exit;
    }

    private function checkAntiguedad($funcionario)
    {
        // Lógica para verificar antigüedad en el grado
        // Ejemplo: debe tener más de 1 año en el grado actual
        if (empty($funcionario['FECH_ASC'])) return false;
        
        $fechaAscenso = new DateTime($funcionario['FECH_ASC']);
        $hoy = new DateTime();
        $diff = $hoy->diff($fechaAscenso);
        return $diff->y >= 1; // Ajustar requisito real
    }

    private function mostrarFormulario($funcionario)
    {
        require 'app/views/postular.php';
    }

    private function rechazar($funcionario, $razon)
    {
        $codigo = $funcionario['COD_FUN'] ?? 'DESCONOCIDO';
        $nombre = $funcionario['NOM_COMPL'] ?? 'DESCONOCIDO';
        $grado = $funcionario['GRADO'] ?? 'N/A';

        $this->postulanteModel->logExclusion(
            $codigo, 
            $nombre, 
            $grado, 
            $razon
        );
        $mensaje = $razon;
        $tipo = "error";
        require 'app/views/mensaje.php';
    }
    
    public function inscribir()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }

        $codigo = $_POST['codigo'] ?? '';
        
        // Verificar que no se inscribió en el intertanto
        if ($this->postulanteModel->isInscrito($codigo)) {
            $inscrito = $this->postulanteModel->isInscrito($codigo);
            require 'app/views/certificado.php';
            exit;
        }
        
        // Obtener datos del postulante para copiar
        $postulante = $this->postulanteModel->findByCodigo($codigo);
        
        if (!$postulante) {
            echo "Error: Postulante no encontrado.";
            exit;
        }
        
        // Insertar en inscritos
        // Campos inscritos: "CODIGO", "NOM_COMPL", "GENERO", "ESTADO", "ZONA", "PREFECTURA", "COMISARIA", "DOTACION", "FECHA_ASC", "GRADO_CURS", "GRADO", "ESCALAFN", "FECH_INSC", "SEXO", "EMAIL", "TELEFONO", "CORREO_PER"
        // Campos postulantes que mapean: COD_FUN -> CODIGO, etc.
        
        try {
            $query = "INSERT INTO inscritos (
                \"CODIGO\", \"NOM_COMPL\", \"GENERO\", \"ESTADO\", \"ZONA\", \"PREFECTURA\", 
                \"COMISARIA\", \"DOTACION\", \"FECHA_ASC\", \"GRADO\", \"ESCALAFN\", 
                \"FECH_INSC\", \"SEXO\", \"EMAIL\", \"TELEFONO\", \"CORREO_PER\"
            ) VALUES (
                :codigo, :nom_compl, :genero, :estado, :zona, :prefectura,
                :comisaria, :dotacion, :fech_asc, :grado, :escalafon,
                :fech_insc, :sexo, :email, :telefono, :correo_per
            )";
            
            $stmt = $this->db->prepare($query);
            
            $fechaInsc = date('Y-m-d H:i:s');
            $escalafon = $postulante['ESCALAF'] ?? ''; 
            
            // Datos del formulario
            $emailCorp = $_POST['email_inst'] ?? ''; // Ajustar ID del input en postular.php
            $emailPers = $_POST['email-pers'] ?? ''; // Ajustar ID
            $telefono = $_POST['tel-ip'] ?? ''; // Ajustar ID
            
            // bindParams
            $stmt->bindParam(':codigo', $postulante['COD_FUN']);
            $stmt->bindParam(':nom_compl', $postulante['NOM_COMPL']);
            $stmt->bindParam(':genero', $postulante['GENERO']);
            $stmt->bindParam(':estado', $postulante['ESTAD']); // O 'INSCRITO'?
            $stmt->bindParam(':zona', $postulante['ZONA']);
            $stmt->bindParam(':prefectura', $postulante['PREFECTURA']);
            $stmt->bindParam(':comisaria', $postulante['COMISARIA']);
            $stmt->bindParam(':dotacion', $postulante['DOTACION']);
            $stmt->bindParam(':fech_asc', $postulante['FECH_ASC']);
            $stmt->bindParam(':grado', $postulante['GRADO']);
            $stmt->bindParam(':escalafon', $escalafon);
            $stmt->bindParam(':fech_insc', $fechaInsc);
            $stmt->bindParam(':sexo', $postulante['GENERO']); // Repetido?
            $stmt->bindParam(':email', $emailCorp);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':correo_per', $emailPers);
            
            $stmt->execute();
            
            // Mostrar certificado
            $inscrito = $this->postulanteModel->isInscrito($codigo);
            $datosInscripcion = $inscrito;
            require 'app/views/certificado.php';
            
        } catch (Exception $e) {
            $mensaje = "Error al inscribir: " . $e->getMessage();
            $tipo = "error";
            require 'app/views/mensaje.php';
        }
    }
}
