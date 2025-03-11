<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemSetting::create([
            'company_name' => 'JAC',
            'logo_path' => 'https://github.com/EmmanWayne/logos_publicos/blob/main/logo_jac.png?raw=true',
            'theme_color' => '#001C4D',
            'is_active' => true,
        ]);
    }
}
