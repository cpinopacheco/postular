<?php

class GradeHelper
{
    /**
     * Estandariza la glosa de un grado eliminando variaciones, sufijos y aplicando mapeos específicos.
     * Basado en la lógica de negocio heredada y expandida para mayor robustez.
     */
    public static function transformar($grado)
    {
        if (empty($grado)) return "";

        // 1. Limpieza base: Mayúsculas y espacios excedentes
        $res = strtoupper(trim($grado));

        // 2. Mapeos Directos / Sinónimos Críticos
        if ($res === 'TENIENTE (I)' || $res === 'TENIENTE') {
            return "TTE.(I)";
        }
        
        if (strpos($res, 'TTE. CORONEL SERV.') !== false) {
            return "TTE. CORONEL SERV.";
        }

        if (strpos($res, 'CARAB. SERVICIOS') !== false) {
            return "CARAB. SERVICIOS";
        }

        if (strpos($res, 'SUBOFICIAL') !== false) {
            return "SUBOFICIAL";
        }

        // 3. Casos con (SEC.) - Estos deben preservarse según la lógica previa
        if (strpos($res, 'SEC.') !== false) {
            if (strpos($res, 'SGTO. 2DO.') !== false) return "SGTO. 2DO.  SEC.";
            if (strpos($res, 'CABO 1RO.') !== false) return "CABO 1RO.   SEC.";
            if (strpos($res, 'CABO 2DO.') !== false) return "CABO 2DO.   SEC.";
        }

        // 4. Estandarización de bloques principales (Sargentos/Cabos/Subof)
        // Eliminamos lo que está entre paréntesis y aplicamos la base limpia con punto final
        if (strpos($res, 'SGTO. 2DO') !== false) return "SGTO. 2DO.";
        if (strpos($res, 'CABO 2DO') !== false) return "CABO 2DO.";
        if (strpos($res, 'CABO 1RO') !== false) return "CABO 1RO.";
        if (strpos($res, 'CARABINERO') !== false) return "CARABINERO";
        if (strpos($res, 'SUBOF') !== false) return "SUBOF.";

        // 5. Regla General: Limpiar cualquier paréntesis residual si no cayó en los mapeos previos
        $res = preg_replace('/\s*\(.*\)/', '', $res);

        return trim($res);
    }
}
