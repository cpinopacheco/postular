<?php

class NormalizationHelper
{
    /**
     * Normaliza los grados (Mantiene lógica de GradeHelper)
     */
    public static function grado($grado)
    {
        if (empty($grado)) return "";
        
        // 1. Normalización básica (mayúsculas, acentos, trim)
        $res = self::condicion($grado);

        // 2. Manejo de variantes de identidad (I) ANTES de quitar paréntesis genéricos
        // Esto evita que "CAPITAN (I)" se convierta en "CAPITAN"
        // Manejamos casos como "TTE.(I)", "TTE (I)", "TTE. (I)"
        $res = preg_replace(['/(\.)?\s*\(I\)/', '/(\.)?\s*I$/'], ' I', $res);

        // 3. Limpieza de "ruido" genérico (lo que está entre paréntesis y puntos al final)
        // Ejemplo: "CABO 2DO. (E.G.)" -> "CABO 2DO."
        $res = preg_replace('/\s*\(.*\)/', '', $res);
        $res = rtrim($res, '.');
        $res = trim($res);

        // 3. Mapeo Granular (Variantes con 'I', 'SEC' o 'SERVICIOS' son distintas)
        
        // Casos de "I" (Instrucción/Intendencia) - PRESERVAR DISTINCIÓN
        if (strpos($res, 'TENIENTE I') !== false || strpos($res, 'TTE I') !== false) return "TTE. I";
        if ($res === 'CAPITAN I') return "CAPITAN I";
        if (strpos($res, 'TTE CRL I') !== false || strpos($res, 'TTE.CRL.I') !== false || strpos($res, 'TENIENTE CORONEL I') !== false) return "TTE. CORONEL I";

        // Casos de SERVICIOS - PRESERVAR DISTINCIÓN
        if (strpos($res, 'TTE CORONEL SERV') !== false || strpos($res, 'TENIENTE CORONEL SERV') !== false) return "TTE. CORONEL SERV.";
        if (strpos($res, 'SGTO 2DO SERVICIOS') !== false) return "SGTO. 2DO. SERVICIOS";
        if (strpos($res, 'CABO 2DO SERVICIOS') !== false) return "CABO 2DO. SERVICIOS";
        if (strpos($res, 'CARAB SERVICIOS') !== false) return "CARAB. SERVICIOS";

        // Casos de SECRETARIADO (SEC) - PRESERVAR DISTINCIÓN
        if (strpos($res, 'SEC') !== false) {
            if (strpos($res, 'SGTO 2DO') !== false) return "SGTO. 2DO. SEC.";
            if (strpos($res, 'CABO 1RO') !== false) return "CABO 1RO. SEC.";
            if (strpos($res, 'CABO 2DO') !== false || $res === '2DO SEC') return "CABO 2DO. SEC.";
        }

        // 4. Mapeo de Grados Base
        if (strpos($res, 'SUBOFICIAL') !== false) return "SUBOFICIAL";
        if (strpos($res, 'SGTO 2DO') !== false) return "SGTO. 2DO.";
        if (strpos($res, 'CABO 2DO') !== false || $res === '2DO') return "CABO 2DO.";
        if (strpos($res, 'CABO 1RO') !== false) return "CABO 1RO.";
        if (strpos($res, 'CARABINERO') !== false) return "CARABINERO";
        if (strpos($res, 'SUBOF') !== false) return "SUBOF.";
        if (strpos($res, 'TENIENTE') !== false || strpos($res, 'TTE') !== false) return "TENIENTE";
        if (strpos($res, 'CAPITAN') !== false) return "CAPITAN";
        if (strpos($res, 'TENIENTE CORONEL') !== false || strpos($res, 'TTE CORONEL') !== false) return "TTE. CORONEL";

        return $res;
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
