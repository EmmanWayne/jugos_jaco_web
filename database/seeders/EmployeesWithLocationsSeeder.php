<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Location;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EmployeesWithLocationsSeeder extends Seeder
{
    public function run(): void
    {
        // Asumiendo que ya existe al menos una sucursal (branch)
        $branchId = 1; // Asegúrate de que este ID existe en la tabla branches

        // Crear 5 empleados
        for ($i = 0; $i < 5; $i++) {
            $employee = Employee::create([
                'first_name' => fake('es_ES')->firstName(),
                'last_name' => fake('es_ES')->lastName() . ' ' . fake('es_ES')->lastName(),
                'phone_number' => fake()->numerify('####-####'),
                'address' => fake('es_ES')->address(),
                'identity' => fake()->unique()->numerify('#############'), // 13 dígitos
                'branch_id' => $branchId,
            ]);

            // Crear ubicaciones para los últimos 5 días
            for ($day = 0; $day < 5; $day++) {
                $date = Carbon::now()->subDays($day);
                
                // Crear 3-6 ubicaciones por día
                for ($j = 0; $j < rand(3, 6); $j++) {
                    $hour = rand(8, 17);
                    $timestamp = $date->copy()->setHour($hour)->setMinute(rand(0, 59));

                    Location::create([
                        'latitude' => 14.6349 + (rand(-50, 50) / 1000),
                        'longitude' => -86.9315 + (rand(-50, 50) / 1000),
                        'model_id' => $employee->id,
                        'model_type' => Employee::class,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ]);
                }
            }
        }
    }
}