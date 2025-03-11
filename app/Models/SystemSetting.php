<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'logo_path',
        'theme_color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'logo_url',
    ];

    // Configuración por defecto
    public static $defaultSettings = [
        'company_name' => 'JAC',
        'logo_path' => 'images/logo.png',
        'theme_color' => '#001C4D',
        'is_active' => true,
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($settings) {
            if ($settings->is_active) {
                // Desactivar todas las demás configuraciones
                self::where('id', '!=', $settings->id)
                    ->update(['is_active' => false]);
            } else {
                // Si se está desactivando esta configuración, verificar si hay otras activas
                $activeCount = self::where('id', '!=', $settings->id)
                    ->where('is_active', true)
                    ->count();
                
                // Si no hay otras configuraciones activas, forzar esta como activa
                if ($activeCount === 0) {
                    $settings->is_active = true;
                }
            }
        });

        // Eliminar el archivo anterior al actualizar
        static::updating(function ($settings) {
            if ($settings->isDirty('logo_path')) {
                $oldPath = $settings->getOriginal('logo_path');
                if ($oldPath && $oldPath !== 'images/logo.png') {
                    Storage::disk('public')->delete($oldPath);
                }
            }
        });

        // Eliminar el archivo al eliminar el registro
        static::deleting(function ($settings) {
            // No eliminar el logo por defecto
            if ($settings->logo_path && $settings->logo_path !== 'images/logo.png') {
                Storage::disk('public')->delete($settings->logo_path);
            }

            // Si es el último registro y está activo, crear uno por defecto
            if (self::count() === 1 && $settings->is_active) {
                self::create(self::$defaultSettings);
            }
        });
    }

    public static function getSettings()
    {
        try {
            // Intentar obtener la configuración activa
            $settings = self::where('is_active', true)->first();
            
            if (!$settings) {
                // Si no hay configuración activa, intentar activar la primera
                $settings = self::first();
                if ($settings) {
                    $settings->update(['is_active' => true]);
                } else {
                    // Si no hay configuraciones, crear una por defecto
                    $settings = self::create(self::$defaultSettings);
                }
            }
            
            return $settings;
        } catch (\Exception $e) {
            // En caso de error, retornar un objeto con la configuración por defecto
            return new static(self::$defaultSettings);
        }
    }

    public function getLogoUrlAttribute()
    {
        if (!$this->logo_path) {
            return asset('images/logo.png');
        }

        if ($this->logo_path === 'images/logo.png') {
            return asset('images/logo.png');
        }

        if (filter_var($this->logo_path, FILTER_VALIDATE_URL)) {
            return $this->logo_path;
        }

        return asset('storage/' . $this->logo_path);
    }

    public function getThemeColors()
    {
        $baseColor = $this->theme_color ?? '#001C4D';
        
        // Generar variantes del color
        return [
            50 => '238, 242, 255',  // Más claro
            100 => '224, 231, 255',
            200 => '199, 210, 254',
            300 => '165, 180, 252',
            400 => '129, 140, 248',
            500 => $baseColor,      // Color base
            600 => $this->adjustColor($baseColor, -20),  // Más oscuro
            700 => $this->adjustColor($baseColor, -40),
            800 => $this->adjustColor($baseColor, -60),
            900 => $this->adjustColor($baseColor, -80),
            950 => $this->adjustColor($baseColor, -100),
        ];
    }

    private function adjustColor($hex, $steps)
    {
        // Convertir hex a RGB
        $hex = str_replace('#', '', $hex);
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
