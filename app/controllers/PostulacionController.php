<?php

require_once 'app/models/Postulante.php';
require_once 'app/models/Proceso.php';
require_once 'app/helpers/NormalizationHelper.php';
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

        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']); // Limpiar error tras leerlo
        require 'app/views/inicio.php';
    }

    public function validar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }

        $rawCodigo = $_POST['codigo'] ?? '';
        // Normalización: Quitar guiones, espacios y pasar a mayúsculas
        $codigo = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $rawCodigo)); 
        
        // Regla 0.A: ¿Proceso abierto?
        if (!$this->procesoModel->isAbierto()) {
            require 'app/views/fin_proceso.php';
            exit;
        }

        // Regla 0.B: ¿Existe en BD? (Validación de Existencia)
        $funcionario = $this->postulanteModel->findByCodigo($codigo);
        if (!$funcionario) {
            error_log("[DEBUG] ERROR: Funcionario no encontrado ($codigo).");
            $this->rechazar(
                null, 
                "El código ingresado no se encuentra habilitado para este proceso. Verifique sus datos e intente nuevamente.",
                "ERROR: El código '$codigo' no existe en la tabla de postulantes habilitados.",
                $codigo
            );
            exit;
        }

        // --- NUEVA REGLA: Validación de Grado Habilitado ---
        $gradoNormalizado = NormalizationHelper::grado($funcionario['GRADO'] ?? '');
        if (!$this->procesoModel->isGradoHabilitado($gradoNormalizado)) {
            error_log("[DEBUG] RECHAZO: Grado '" . $gradoNormalizado . "' no habilitado para este proceso.");
            $this->rechazar(
                $funcionario,
                "Su grado actual (" . $gradoNormalizado . ") no se encuentra habilitado para participar en este proceso de inscripción.",
                "RECHAZO: El grado '" . $gradoNormalizado . "' no está en la lista de grados permitidos."
            );
            exit;
        }

        // Regla 0.C: ¿Ya está inscrito?
        $inscrito = $this->postulanteModel->isInscrito($codigo);
        if ($inscrito) {
            $datosInscripcion = $inscrito;
            require 'app/views/certificado.php';
            exit;
        }

        // Regla 0.D: ¿Es reincorporado / recurso?
        $esReincorporado = (NormalizationHelper::siNo($funcionario['REI_REC'] ?? 'NO') === 'SI');
        
        if ($esReincorporado) {
            $this->mostrarFormulario($funcionario);
            exit;
        }
        
        // Si NO es reincorporado, continuar con validaciones de notas
        
        // LÓGICA DE VALIDACIÓN COMBINADA (Rama A: Sin Notas + Rama B: Logica Estricta Anterior)
        error_log("[DEBUG] >>> INICIO VALIDACIÓN para Funcionario: " . $codigo);
        
        // 1. Obtención y Normalización del grado actual
        $gradoOriginal = $funcionario['GRADO'] ?? '';
        $gradoActual = NormalizationHelper::grado($gradoOriginal);
        
        if (empty($gradoActual)) {
            error_log("[DEBUG] ERROR: Grado no determinado.");
            $this->rechazar(
                $funcionario, 
                "No se pudo determinar su grado actual. Por favor, comuníquese con la Sección de Perfeccionamiento.",
                "ERROR: Fallo en normalización o grado nulo en la tabla de postulantes."
            );
            exit;
        }
        error_log("[DEBUG] [Paso 1] Grado detectado: '" . $gradoOriginal . "' -> Normalizado: '" . $gradoActual . "'");
        
        // 2. Selección de notas del grado actual
        $notas = $this->postulanteModel->getNotasByGrado($codigo, $gradoActual);
        
        // --- RAMA A: CASO SI NO TIENE NOTAS EN EL GRADO ACTUAL ---
        if (empty($notas)) {
            error_log("[DEBUG] [Rama A] No se encontraron notas para el grado '" . $gradoActual . "'. Evaluando antigüedad desde ascenso.");
            
            // Regla A.1: Tiempo desde Ascenso
            $fechaAscensoStr = $funcionario['FECH_ASC'] ?? '';
            if (empty($fechaAscensoStr)) {
                error_log("[DEBUG] [Regla A.1] No tiene FECH_ASC. Permitiendo inscripción por defecto.");
                $this->mostrarFormulario($funcionario);
                exit;
            }

            $fechaAscenso = new DateTime($fechaAscensoStr);
            $hoy = new DateTime();
            $diferencia = $hoy->diff($fechaAscenso);
            
            // Determinar límite según grado (Capitán: 3 años, Resto: 2 años)
            $esCapitan = (strpos($gradoActual, 'CAPITAN') !== false);
            $limiteAnios = $esCapitan ? 3 : 2;

            error_log("[DEBUG] [Rama A.2] Tiempo desde ascenso (" . $fechaAscensoStr . "): " . $diferencia->y . " años, " . $diferencia->m . " meses, " . $diferencia->d . " días. Límite para $gradoActual: $limiteAnios años.");

            // La regla es "más de X años", es decir, X años y un día o más.
            // Si tiene exactamente X años, 0 meses y 0 días, ES el aniversario y todavía puede postular.
            $haSuperadoLimite = ($diferencia->y > $limiteAnios) || ($diferencia->y == $limiteAnios && ($diferencia->m > 0 || $diferencia->d > 0));

            if ($haSuperadoLimite) {
                error_log("[DEBUG] !!! RECHAZO: Han pasado más de $limiteAnios años ($diferencia->y años, $diferencia->m meses, $diferencia->d días) desde su ascenso sin registros de notas.");
                $this->rechazar(
                    $funcionario, 
                    "Su fecha de ascenso supera el límite de $limiteAnios años permitidos (tiene $diferencia->y años y " . ($diferencia->d + ($diferencia->m * 30)) . " días) para postular sin antecedentes de notas en su grado actual.",
                    "RECHAZO: Más de $limiteAnios años desde el ascenso ($fechaAscensoStr) sin registros de notas.",
                    $funcionario['COD_FUN']
                );
                exit;
            } else {
                error_log("[DEBUG] >>> APROBADO: Se encuentra dentro del plazo de $limiteAnios años.");
                $this->mostrarFormulario($funcionario);
                exit;
            }
        }

        // --- RAMA B: CASO SI SÍ TIENE NOTAS (Lógica Anterior Verificada) ---
        error_log("[DEBUG] [Rama B] Se encontraron " . count($notas) . " registro(s) de notas. Evaluando normativa...");

        // Regla B.1: Mérito (Prioridad MÁXIMA)
        $tieneMerito = false;
        foreach ($notas as $nota) {
            if (NormalizationHelper::condicion($nota['condicion'] ?? '') === 'MERITO') {
                $tieneMerito = true;
                error_log("[DEBUG] [Regla 1] MÉRITO DETECTADO: El funcionario ya cuenta con mérito en su grado actual (" . ($nota['ano'] ?? 'N/A') . ").");
                break;
            }
        }

        if ($tieneMerito) {
            error_log("[DEBUG] !!! RECHAZO: El funcionario ya aprobó/cursó con MÉRITO.");
            $this->rechazar(
                $funcionario, 
                "Usted ya cuenta con la condición de MÉRITO en su grado actual, habiendo cumplido el objetivo de este proceso.",
                "RECHAZO: El funcionario ya posee MÉRITO en el grado actual (Validación Regla B.1)."
            );
            exit;
        }

        // Regla B.2: Antigüedad (El "Comodín")
        $tieneAntigüedad = false;
        foreach ($notas as $nota) {
            $condicionNota = NormalizationHelper::condicion($nota['condicion'] ?? '');
            if ($condicionNota === 'ANTIGUEDAD') {
                $tieneAntigüedad = true;
                error_log("[DEBUG] [Regla B.2] ANTIGÜEDAD DETECTADA: Identificada para prioridad sobre lagunas.");
                break;
            }
        }
        
        // 3. Límite máximo de repeticiones (3 consecutivas)
        error_log("[DEBUG] [Regla 3] Evaluando historial de reprobaciones consecutivas...");
        // Regla B.3: 3 Reprobaciones Consecutivas
        $maxConsecutivas = 0;
        $actualConsecutivas = 0;
        
        foreach ($notas as $nota) {
            $anio = (int)($nota['ano'] ?? 0);
            $condicion = NormalizationHelper::condicion($nota['condicion'] ?? '');
            $esReprobado = ($condicion === 'REPROBADO');
            
            if ($esReprobado) {
                $actualConsecutivas++;
                $maxConsecutivas = max($maxConsecutivas, $actualConsecutivas);
                error_log("[DEBUG] [Regla B.3] -> Año " . $anio . ": REPROBADO. Contador: " . $actualConsecutivas);
            } else {
                $actualConsecutivas = 0;
            }
        }
        
        if ($maxConsecutivas >= 3) {
            error_log("[DEBUG] !!! RECHAZO CRÍTICO: 3 o más reprobaciones consecutivas.");
            $this->rechazar(
                $funcionario, 
                "Usted ha alcanzado el límite máximo de 3 reprobaciones consecutivas en su grado actual. No es posible postular.",
                "RECHAZO: Límite de 3 reprobaciones consecutivas detectado (Regla B.3)."
            );
            exit;
        }

        // Regla B.4: Continuidad Anual ("Laguna")
        if ($tieneAntigüedad) {
            error_log("[DEBUG] [Regla B.4] SALTANDO REGLA DE LAGUNA: El funcionario cuenta con Antigüedad.");
            $this->mostrarFormulario($funcionario);
            exit;
        }

        $ultimaReprobacionAnio = 0;
        foreach ($notas as $nota) {
            if (NormalizationHelper::condicion($nota['condicion']) === 'REPROBADO') {
                $ultimaReprobacionAnio = max($ultimaReprobacionAnio, (int)$nota['ano']);
            }
        }

        if ($ultimaReprobacionAnio === 0) {
            error_log("[DEBUG] [Regla B.4] Sin reprobaciones en el historial. APROBADO.");
            $this->mostrarFormulario($funcionario);
            exit;
        } else {
            $anioProceso = (int)date('Y'); 
            if ($anioProceso === ($ultimaReprobacionAnio + 1)) {
                error_log("[DEBUG] [Regla B.4] Continuidad validada (postula al año siguiente de reprobar).");
                $this->mostrarFormulario($funcionario);
                exit;
            } else if ($anioProceso > ($ultimaReprobacionAnio + 1)) {
                error_log("[DEBUG] [Regla B.4] !!! RECHAZO: Laguna detectada tras reprobación en " . $ultimaReprobacionAnio);
                $this->rechazar(
                    $funcionario, 
                    "Su historial registra un año sin postulación. Debía postular de manera consecutiva tras su última reprobación.",
                    "RECHAZO: Laguna detectada. No postuló el año consecutivo a su última reprobación de $ultimaReprobacionAnio."
                );
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
        // Normalizar el grado antes de mostrarlo en la vista
        $funcionario['GRADO_MOSTRAR'] = NormalizationHelper::grado($funcionario['GRADO'] ?? '');
        require 'app/views/postular.php';
    }

    private function rechazar($funcionario, $userMessage, $techReason, $overrideCodigo = null)
    {
        $codigo = $funcionario['COD_FUN'] ?? ($overrideCodigo ?? 'DESCONOCIDO');
        $nombre = $funcionario['NOM_COMPL'] ?? 'USUARIO NO ENCONTRADO';
        $grado = $funcionario['GRADO'] ?? 'N/A';

        // Guardamos el motivo TÉCNICO en la base de datos (Exclusiones)
        $this->postulanteModel->logExclusion(
            $codigo, 
            $nombre, 
            $grado, 
            $techReason
        );
        
        // Guardamos el mensaje amigable para el USUARIO en la sesión
        $_SESSION['error'] = $userMessage;
        
        // Redirigir de vuelta al inicio
        header('Location: /index.php');
        exit;
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
            // 1. Calcular el siguiente ID correlativo de forma manual para evitar desajustes de secuencia
            $stmtId = $this->db->query('SELECT COALESCE(MAX("ID_INSC"), 0) + 1 as next_id FROM inscritos');
            $nextId = $stmtId->fetch(PDO::FETCH_ASSOC)['next_id'];

            // 2. Preparar inserción (grado_curs debe ser igual a grado, SEXO siempre NULL)
            $query = "INSERT INTO inscritos (
                \"ID_INSC\", \"CODIGO\", \"NOM_COMPL\", \"GENERO\", \"ESTADO\", \"ZONA\", \"PREFECTURA\", 
                \"COMISARIA\", \"DOTACION\", \"FECHA_ASC\", \"GRADO\", \"GRADO_CURS\", \"ESCALAFN\", 
                \"FECH_INSC\", \"SEXO\", \"EMAIL\", \"TELEFONO\", \"CORREO_PER\"
            ) VALUES (
                :id_insc, :codigo, :nom_compl, :genero, :estado, :zona, :prefectura,
                :comisaria, :dotacion, :fech_asc, :grado, :grado_curs, :escalafon,
                :fech_insc, :sexo, :email, :telefono, :correo_per
            )";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_insc', $nextId);
            
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
            
            // Guardar el grado normalizado para consistencia en la BD de inscritos
            $gradoInscrito = NormalizationHelper::grado($postulante['GRADO'] ?? '');
            $stmt->bindParam(':grado', $gradoInscrito);
            $stmt->bindParam(':grado_curs', $gradoInscrito);
            
            $stmt->bindParam(':escalafon', $escalafon);
            $stmt->bindParam(':fech_insc', $fechaInsc);
            
            // Forzar SEXO a NULL según requerimiento
            $nullValue = null;
            $stmt->bindParam(':sexo', $nullValue, PDO::PARAM_NULL);
            
            $stmt->bindParam(':email', $emailCorp);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':correo_per', $emailPers);
            
            $stmt->execute();
            
            // Mostrar certificado
            $inscrito = $this->postulanteModel->isInscrito($codigo);
            $datosInscripcion = $inscrito;
            require 'app/views/certificado.php';
            
        } catch (Exception $e) {
            // Log técnico para el desarrollador
            error_log(" [CRÍTICO] Error al inscribir funcionario: " . $e->getMessage());
            error_log($e->getTraceAsString());

            $mensaje = "Lo sentimos, ha ocurrido un problema técnico al procesar su inscripción. Por favor, intente más tarde o contacte a soporte.";
            $tipo = "error";
            require 'app/views/mensaje.php';
        }
    }
}
