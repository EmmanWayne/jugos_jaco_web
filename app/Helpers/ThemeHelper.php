<?php

namespace App\Helpers;

use App\Models\SystemSetting;

class ThemeHelper
{
    public static function getThemeColor()
    {
        $settings = SystemSetting::getSettings();
        return $settings ? $settings->theme_color : '#001C4D';
    }

    public static function getThemeColorVariant($variant = 'base')
    {
        $baseColor = self::getThemeColor();

        switch ($variant) {
            case 'darker':
                return self::adjustColor($baseColor, -20);
            case 'darkest':
                return self::adjustColor($baseColor, -40);
            case 'lighter':
                return self::adjustColor($baseColor, 20);
            case 'lightest':
                return self::adjustColor($baseColor, 40);
            default:
                return $baseColor;
        }
    }

    public static function adjustColor($hex, $steps)
    {
        // Convertir hex a RGB
        $hex = str_replace('#', '', $hex);
        
        // Asegurarse de que el hex tenga 6 caracteres
        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Ajustar cada componente
        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));

        // Convertir de vuelta a hex
        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }
} 