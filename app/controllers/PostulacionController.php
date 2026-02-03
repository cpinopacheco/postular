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
            $mensaje = "El código ingresado no se encuentra en la base de datos de funcionarios habilitados.";
            $tipo = "error";
            require 'app/views/mensaje.php';
            exit;
        }

        // 4. ¿Es reincorporado / recurso?
        // Asumiendo que 'ESTAD' o alguna columna indica esto. 
        // Lógica PLACEHOLDER: Si ESTAD contiene 'REINC' o 'RECURSO'
        // Ajustar según valores reales de la columna ESTAD
        $esReincorporado = (stripos($funcionario['ESTAD'], 'REINC') !== false || stripos($funcionario['ESTAD'], 'REC') !== false);
        
        if ($esReincorporado) {
            $this->mostrarFormulario($funcionario);
            exit;
        }

        // Validaciones de Requisitos (Si no es reincorporado)
        $notas = $this->postulanteModel->getNotas($codigo);
        
        if (!empty($notas)) {
            // Tiene notas
            
            // ¿Tiene mérito? 
            // CONDICION == 'APROBADO' es un buen criterio por defecto
            $ultimaNota = end($notas); // Asumiendo orden, si no ordenar
            $tieneMerito = ($ultimaNota['condicion'] ?? '') === 'APROBADO'; // Ajustar columna (user dijo: condicion)
            
            if (!$tieneMerito) {
                // Si la última nota no es aprobada, quizas rechazar?
                // El diagrama dice "¿Tiene mérito? No -> Tiene laguna...?"
                // Así que si NO tiene mérito, seguimos el flujo de la izquierda
            }

            // ¿Tiene laguna en el proceso?
            // Verificamos si los años son consecutivos
            $anios = array_column($notas, 'ano');
            sort($anios);
            $tieneLaguna = false;
            for ($i = 0; $i < count($anios) - 1; $i++) {
                if ($anios[$i + 1] - $anios[$i] > 1) {
                    $tieneLaguna = true;
                    break;
                }
            }

            // Diagrama: ¿Tiene mérito?
            if ($tieneMerito) {
               // SI tiene mérito -> Se puede inscribir?
               // El diagrama muestra: ¿Tiene mérito? -> Sí -> ¿Tiene notas? (loop?)
               // No, el diagrama es:
               // ¿Tiene notas? -> Sí -> ¿Tiene mérito? -> Sí -> Se puede inscribir (según rama izquierda que vuelve a "Se puede inscribir"?)
               // Espera, el diagrama dice:
               // ¿Tiene notas? -> Sí -> ¿Tiene mérito? -> Sí -> Se puede inscribir (No hay cajita, conecta a la linea de abajo que dice "Se puede inscribir"?)
               // NO, mira la linea: Sí (Mérito) -> Se puede inscribir. 
               // No (Mérito) -> ¿Tiene laguna?
               
               $this->mostrarFormulario($funcionario);
               exit;
            } 
            
            // NO tiene mérito
            // ¿Tiene laguna en el proceso?
            if ($tieneLaguna) {
               // Sí laguna -> ¿Tiene antigüedad en el grado?
               if ($this->checkAntiguedad($funcionario)) {
                    $this->mostrarFormulario($funcionario); // Sí antigüedad
               } else {
                    $this->rechazar($funcionario, "Tiene laguna en el proceso y no cumple antigüedad."); // No antigüedad
               }
               exit;
            }
            
            // No laguna -> ¿Ha reprobado más de 2 veces?
            $reprobaciones = 0;
            foreach ($notas as $nota) {
                if (($nota['nota'] ?? 0) < 4.0 || ($nota['condicion'] ?? '') === 'REPROBADO') {
                    $reprobaciones++;
                }
            }
            
            if ($reprobaciones > 2) {
               // Sí > 2 reprobaciones -> ¿Tiene antigüedad en el grado?
               if ($this->checkAntiguedad($funcionario)) {
                    $this->mostrarFormulario($funcionario);
               } else {
                    $this->rechazar($funcionario, "Ha reprobado más de 2 veces y no cumple antigüedad.");
               }
               exit;
            }
            
            // No > 2 reprobaciones -> Se puede inscribir
            $this->mostrarFormulario($funcionario);
            exit;

        } else {
            // NO tiene notas
            // ... (lógica existente correcta)
             // ¿Más de 2 años desde el ascenso?
            // FECH_ASC vs Hoy
            $fechaAscenso = new DateTime($funcionario['FECH_ASC']);
            $hoy = new DateTime();
            $diferencia = $hoy->diff($fechaAscenso);
            
            if ($diferencia->y > 2) {
                $this->rechazar($funcionario, "Han pasado más de 2 años desde su último ascenso sin historial de notas.");
                exit;
            } else {
                $this->mostrarFormulario($funcionario);
                exit;
            }
        }
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
        $this->postulanteModel->logExclusion(
            $funcionario['COD_FUN'], 
            $funcionario['NOM_COMPL'], 
            $funcionario['GRADO'], 
            $razon
        );
        $mensaje = "No puede inscribirse: " . $razon;
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
