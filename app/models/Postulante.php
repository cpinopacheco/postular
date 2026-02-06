<?php

class Postulante
{
    private $conn;
    private $table_postulantes = 'postulantes';
    private $table_inscritos = 'inscritos';
    private $table_notas = 'notas_anteriores';
    private $table_exclusiones = 'exclusiones';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Busca al funcionario por su código en la tabla de habilitados para postular.
     */
    public function findByCodigo($codigo)
    {
        $query = "SELECT * FROM " . $this->table_postulantes . " WHERE \"COD_FUN\" = :codigo LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Verifica si el funcionario ya se encuentra inscrito para el proceso actual.
     */
    public function isInscrito($codigo)
    {
        $query = "SELECT * FROM " . $this->table_inscritos . " WHERE \"CODIGO\" = :codigo LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el historial de notas del funcionario filtrado por grado.
     */
    public function getNotasByGrado($codigo, $grado)
    {
        // Importar helper si no está cargado (por seguridad en el modelo)
        if (!class_exists('GradeHelper')) {
            require_once 'app/helpers/GradeHelper.php';
        }
        
        $gradoNormalizado = GradeHelper::transformar($grado);
        
        // Solo las notas del grado actual son válidas según nueva normativa
        $query = "SELECT * FROM " . $this->table_notas . " 
                  WHERE codigo = :codigo 
                  AND grado = :grado 
                  ORDER BY ano ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':grado', $gradoNormalizado);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Registra un evento de exclusión (log de rechazo).
     */
    public function logExclusion($codigo, $nombre, $grado, $razon)
    {
        $query = "INSERT INTO " . $this->table_exclusiones . " 
                  (fecha, codigo, nombre, grado, razon) 
                  VALUES (:fecha, :codigo, :nombre, :grado, :razon)";
        
        $stmt = $this->conn->prepare($query);
        
        // Asumiendo formato de fecha timestamp o date según la BD
        $fecha = date('Y-m-d H:i:s'); 
        
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':grado', $grado);
        $stmt->bindParam(':razon', $razon);
        
        return $stmt->execute();
    }
}
