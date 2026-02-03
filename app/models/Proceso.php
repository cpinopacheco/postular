<?php

class Proceso
{
    // Fechas desde variables de entorno (con fallback a 2026)
    private $fecha_inicio;
    private $fecha_fin;

    public function __construct() {
        $this->fecha_inicio = getenv('PROCESO_FECHA_INICIO') ?: '2026-01-01';
        $this->fecha_fin = getenv('PROCESO_FECHA_FIN') ?: '2026-12-31';
    }

    public function isAbierto()
    {
        $hoy = date('Y-m-d');
        return ($hoy >= $this->fecha_inicio && $hoy <= $this->fecha_fin);
    }
}
