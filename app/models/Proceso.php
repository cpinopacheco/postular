<?php

class Proceso
{
    // Fechas desde variables de entorno (con fallback a 2026)
    private $fecha_inicio;
    private $fecha_fin;
    private $grados_habilitados;

    public function __construct() {
        $this->fecha_inicio = getenv('PROCESO_FECHA_INICIO') ?: '2026-01-01';
        $this->fecha_fin = getenv('PROCESO_FECHA_FIN') ?: '2026-12-31';
        
        // Cargar grados habilitados (si no se definen, todos están permitidos por defecto)
        $gradosRaw = getenv('PROCESO_GRADOS_HABILITADOS') ?: '';
        // Normalizamos cada grado de la lista usando el helper (quita comillas, espacios y acentos)
        $this->grados_habilitados = !empty($gradosRaw) ? array_map(function($g) {
            $cleaned = trim($g, " \t\n\r\0\x0B\"'");
            return NormalizationHelper::grado($cleaned);
        }, explode(',', $gradosRaw)) : [];
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
        // Si no hay lista definida, se entiende que todos los grados pueden postular
        if (empty($this->grados_habilitados)) {
            return true;
        }

        return in_array(strtoupper($grado), $this->grados_habilitados);
    }
}
