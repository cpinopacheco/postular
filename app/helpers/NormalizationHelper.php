<?php

class NormalizationHelper
{
    /**
     * Normaliza los grados (Mantiene lógica de GradeHelper)
     */
    public static function grado($grado)
    {
        if (empty($grado)) return "";
        $res = strtoupper(trim($grado));

        if ($res === 'TENIENTE (I)' || $res === 'TENIENTE') return "TTE.(I)";
        if (strpos($res, 'TTE. CORONEL SERV.') !== false) return "TTE. CORONEL SERV.";
        if (strpos($res, 'CARAB. SERVICIOS') !== false) return "CARAB. SERVICIOS";
        if (strpos($res, 'SUBOFICIAL') !== false) return "SUBOFICIAL";

        if (strpos($res, 'SEC.') !== false) {
            if (strpos($res, 'SGTO. 2DO.') !== false) return "SGTO. 2DO.  SEC.";
            if (strpos($res, 'CABO 1RO.') !== false) return "CABO 1RO.   SEC.";
            if (strpos($res, 'CABO 2DO.') !== false) return "CABO 2DO.   SEC.";
        }

        if (strpos($res, 'SGTO. 2DO') !== false) return "SGTO. 2DO.";
        if (strpos($res, 'CABO 2DO') !== false) return "CABO 2DO.";
        if (strpos($res, 'CABO 1RO') !== false) return "CABO 1RO.";
        if (strpos($res, 'CARABINERO') !== false) return "CARABINERO";
        if (strpos($res, 'SUBOF') !== false) return "SUBOF.";

        $res = preg_replace('/\s*\(.*\)/', '', $res);
        return trim($res);
    }

    /**
     * Normaliza la condición de la nota (Maneja acentos, diéresis y limpieza)
     * Ejemplo: 'AntigüeDaD ' -> 'ANTIGUEDAD'
     */
    public static function condicion($condicion)
    {
        if (empty($condicion)) return "";
        
        $res = trim($condicion);
        
        // Mapa completo de normalización de caracteres con acentos/diéresis
        $acentos = [
            'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C',
            'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I',
            'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
            'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i',
            'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o',
            'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y'
        ];
        
        $res = strtr($res, $acentos);
        
        return strtoupper($res);
    }

    /**
     * Normaliza indicadores de SI/NO
     */
    public static function siNo($val)
    {
        if (empty($val)) return "NO";
        
        $res = self::condicion($val); // Reutilizamos limpieza de acentos
        
        if ($res === 'SI' || $res === 'SÍ' || $res === 'S') return "SI";
        
        return "NO";
    }
}
