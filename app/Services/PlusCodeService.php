<?php

namespace App\Services;

/**
 * Servicio para manejar la generación y decodificación de Plus Codes
 * (Open Location Codes)
 */
class PlusCodeService
{
    // El alfabeto utilizado por los Plus Codes
    private const ALPHABET = '23456789CFGHJMPQRVWX';
    
    // Longitud de código completo (sin separador)
    private const CODE_LENGTH = 10;
    
    // Posición del separador
    private const SEPARATOR_POSITION = 8;
    
    /**
     * Convierte una latitud/longitud a un Open Location Code (Plus Code)
     * siguiendo la implementación oficial de Google.
     *
     * @param float $latitude La latitud en grados (se recorta a [-90,90])
     * @param float $longitude La longitud en grados (se normaliza a [-180,180))
     * @param int|null $codeLength La longitud deseada del código (sin contar el separador).
     *                             Valores válidos: 10, 11, 13 o 15. Por defecto se usa 10.
     * @return string El Plus Code generado (incluye el separador en la posición 8)
     * @throws \Exception Si los parámetros no son válidos.
     */
    public function encode(float $latitude, float $longitude, int $codeLength = null): string
    {
        // ---------- Constantes ----------
        $SEPARATOR           = '+';
        $SEPARATOR_POSITION  = 8;
        $PADDING_CHARACTER   = '0';
        $CODE_ALPHABET       = '23456789CFGHJMPQRVWX';
        $ENCODING_BASE       = strlen($CODE_ALPHABET); // 20
        $LATITUDE_MAX        = 90;
        $LONGITUDE_MAX       = 180;
        $MIN_DIGIT_COUNT     = 2;
        $MAX_DIGIT_COUNT     = 15;
        $PAIR_CODE_LENGTH    = 10;  // Dígitos en la parte de par
        $PAIR_FIRST_PLACE_VALUE = pow($ENCODING_BASE, ($PAIR_CODE_LENGTH / 2 - 1)); // pow(20,4)
        $PAIR_PRECISION      = pow($ENCODING_BASE, 3); // 20^3 = 8000
        $PAIR_RESOLUTIONS    = [20.0, 1.0, 0.05, 0.0025, 0.000125];
        $GRID_CODE_LENGTH    = $MAX_DIGIT_COUNT - $PAIR_CODE_LENGTH; // 15 - 10 = 5
        $GRID_COLUMNS        = 4;
        $GRID_ROWS           = 5;
        $GRID_LAT_FIRST_PLACE_VALUE = pow($GRID_ROWS, ($GRID_CODE_LENGTH - 1)); // 5^(5-1)=5^4=625
        $GRID_LNG_FIRST_PLACE_VALUE = pow($GRID_COLUMNS, ($GRID_CODE_LENGTH - 1)); // 4^4=256
        $FINAL_LAT_PRECISION = $PAIR_PRECISION * pow($GRID_ROWS, ($MAX_DIGIT_COUNT - $PAIR_CODE_LENGTH)); // 8000 * 5^5 = 25000000
        $FINAL_LNG_PRECISION = $PAIR_PRECISION * pow($GRID_COLUMNS, ($MAX_DIGIT_COUNT - $PAIR_CODE_LENGTH)); // 8000 * 4^5 = 8192000
        $MIN_TRIMMABLE_CODE_LEN = 6;
        
        // ---------- Parámetros y validación ----------
        if (is_null($codeLength)) {
            $codeLength = 10; // Precisión normal.
        } else {
            $codeLength = min($MAX_DIGIT_COUNT, $codeLength);
        }
        if ($codeLength < $MIN_DIGIT_COUNT ||
            ($codeLength < $PAIR_CODE_LENGTH && ($codeLength % 2) == 1)) {
            throw new \Exception('Invalid Open Location Code length');
        }
        
        // Convertir a número y comprobar
        $latitude = (float)$latitude;
        $longitude = (float)$longitude;
        if (is_nan($latitude) || is_nan($longitude)) {
            throw new \Exception('Parameters are not valid numbers');
        }
        
        // ---------- Ajuste de coordenadas ----------
        $latitude = $this->clipLatitude($latitude);       // [–90, 90]
        $longitude = $this->normalizeLongitude($longitude); // [-180,180)
        // Si la latitud es 90, la reducimos un poco.
        if ($latitude == 90) {
            $latitude = $latitude - $this->computeLatitudePrecision($codeLength, $ENCODING_BASE, $GRID_ROWS);
        }
        // Convertir a valores positivos
        $latAdjusted = $latitude + $LATITUDE_MAX;   // [0, 180]
        $lngAdjusted = $longitude + $LONGITUDE_MAX;   // [0, 360]
        
        // ---------- Multiplicar por la precisión final y convertir a enteros ----------
        $latVal = (int)floor(round($latAdjusted * $FINAL_LAT_PRECISION * 1e6) / 1e6);
        $lngVal = (int)floor(round($lngAdjusted * $FINAL_LNG_PRECISION * 1e6) / 1e6);
        
        $code = ""; // Se construirá el código final.
        
        // ---------- CÓDIGO GRID (si se pide precisión mayor que 10 dígitos) ----------
        if ($codeLength > $PAIR_CODE_LENGTH) {
            // En lugar de siempre usar 5 dígitos extra, usamos solo los necesarios:
            $gridDigitCount = $codeLength - $PAIR_CODE_LENGTH;
            for ($i = 0; $i < $gridDigitCount; $i++) {
                $latDigit = $latVal % $GRID_ROWS;
                $lngDigit = $lngVal % $GRID_COLUMNS;
                $ndx = $latDigit * $GRID_COLUMNS + $lngDigit;
                $code = $CODE_ALPHABET[$ndx] . $code;
                $latVal = (int)floor($latVal / $GRID_ROWS);
                $lngVal = (int)floor($lngVal / $GRID_COLUMNS);
            }
        } else {
            // Si no se requiere la parte GRID, reducir el valor
            $latVal = (int)floor($latVal / pow($GRID_ROWS, $GRID_CODE_LENGTH));
            $lngVal = (int)floor($lngVal / pow($GRID_COLUMNS, $GRID_CODE_LENGTH));
        }
        
        // ---------- CÓDIGO DE PAREJA ----------
        for ($i = 0; $i < ($PAIR_CODE_LENGTH / 2); $i++) {
            $code = $CODE_ALPHABET[$lngVal % $ENCODING_BASE] . $code;
            $code = $CODE_ALPHABET[$latVal % $ENCODING_BASE] . $code;
            $latVal = (int)floor($latVal / $ENCODING_BASE);
            $lngVal = (int)floor($lngVal / $ENCODING_BASE);
        }
        
        // ---------- Inserción del separador ----------
        $code = substr($code, 0, $SEPARATOR_POSITION) .
                $SEPARATOR .
                substr($code, $SEPARATOR_POSITION);
        
        // Devolver la parte solicitada: si se requieren todos los dígitos (más el separador)
        if ($codeLength >= $SEPARATOR_POSITION) {
            return substr($code, 0, $codeLength + 1);
        } else {
            $pad = str_repeat($PADDING_CHARACTER, $SEPARATOR_POSITION - $codeLength);
            return substr($code, 0, $codeLength) . $pad . $SEPARATOR;
        }
    }

    /* Funciones auxiliares privadas */

    private function clipLatitude(float $latitude): float
    {
        return min(90, max(-90, $latitude));
    }

    private function normalizeLongitude(float $longitude): float
    {
        while ($longitude < -180) {
            $longitude += 360;
        }
        while ($longitude >= 180) {
            $longitude -= 360;
        }
        return $longitude;
    }

    private function computeLatitudePrecision(int $codeLength, int $ENCODING_BASE, int $GRID_ROWS): float
    {
        if ($codeLength <= 10) {
            return pow($ENCODING_BASE, floor($codeLength / -2 + 2));
        }
        return pow($ENCODING_BASE, -3) / pow($GRID_ROWS, $codeLength - 10);
    }
}