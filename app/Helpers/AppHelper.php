<?php

namespace App\Helpers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Schema;

class AppHelper
{
    public static function getAppName()
    {
        try {
            if (Schema::hasTable('system_settings')) {
                $settings = SystemSetting::getSettings();
                if ($settings && $settings->company_name) {
                    return $settings->company_name;
                }
            }
        } catch (\Exception $e) {
            // Si hay algún error, retornamos el valor por defecto
        }
        
        return config('app.name', 'JAC');
    }
} 