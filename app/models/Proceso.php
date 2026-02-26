<?php

class Proceso
{
    private $fecha_inicio;
    private $fecha_fin;
    private $grados_habilitados;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->loadConfiguration();
    }

    private function loadConfiguration() {
        $this->fecha_inicio = $this->getConfigValue('PROCESO_FECHA_INICIO') ?: '2026-01-01';
        $this->fecha_fin = $this->getConfigValue('PROCESO_FECHA_FIN') ?: '2026-12-31';
        
        $gradosRaw = $this->getConfigValue('PROCESO_GRADOS_HABILITADOS') ?: '';
        
        $this->grados_habilitados = !empty($gradosRaw) ? array_map(function($g) {
            $cleaned = trim($g, " \t\n\r\0\x0B\"'");
            return NormalizationHelper::grado($cleaned);
        }, explode(',', $gradosRaw)) : [];
    }

    private function getConfigValue($clave) {
        $stmt = $this->db->prepare("SELECT valor FROM configuracion WHERE clave = :clave");
        $stmt->bindParam(':clave', $clave);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function isAbierto()
    {
        $hoy = date('Y-m-d');
        return ($hoy >= $this->fecha_inicio && $hoy <= $this->fecha_fin);
    }

    /**
     * Verifica si el grado normalizado del funcionario está habilitado para el proceso.
     */
    public function isGradoHabilitado($grado)
    {
        // Si no hay lista definida, se entiende que el proceso está cerrado para todos los grados
        if (empty($this->grados_habilitados)) {
            return false;
        }

        return in_array(strtoupper($grado), $this->grados_habilitados);
    }

    public function getFechaFin() {
        return $this->fecha_fin;
    }
}
