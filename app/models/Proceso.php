<?php

class Proceso
{
    // Fechas de ejemplo, idealmente deberían venir de BD o configuración
    private $fecha_inicio = '2026-01-01';
    private $fecha_fin = '2026-12-31';

    public function isAbierto()
    {
        $hoy = date('Y-m-d');
        return ($hoy >= $this->fecha_inicio && $hoy <= $this->fecha_fin);
    }
}
