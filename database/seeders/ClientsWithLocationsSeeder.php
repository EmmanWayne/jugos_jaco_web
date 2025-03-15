<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Location;
use Illuminate\Database\Seeder;

class ClientsWithLocationsSeeder extends Seeder
{
    public function run(): void
    {
        // Departamentos y municipios de Honduras
        $locations = [
            'Francisco Morazán' => [
                'city' => 'Tegucigalpa',
                'lat' => 14.0723,
                'lng' => -87.1921,
                'townships' => ['Tegucigalpa', 'Comayagüela', 'Santa Lucía', 'Valle de Ángeles']
            ],
            'Cortés' => [
                'city' => 'San Pedro Sula',
                'lat' => 15.5049,
                'lng' => -88.0250,
                'townships' => ['San Pedro Sula', 'Choloma', 'Villanueva', 'La Lima']
            ],
            'Atlántida' => [
                'city' => 'La Ceiba',
                'lat' => 15.7835,
                'lng' => -86.7830,
                'townships' => ['La Ceiba', 'Tela', 'El Porvenir', 'Arizona']
            ],
        ];

        foreach ($locations as $department => $data) {
            foreach ($data['townships'] as $township) {
                for ($i = 0; $i < rand(2, 4); $i++) {
                    $client = Client::create([
                        'first_name' => fake('es_ES')->firstName(),
                        'last_name' => fake('es_ES')->lastName() . ' ' . fake('es_ES')->lastName(),
                        'employee_id' => rand(1, 5), // Asumiendo que hay 5 empleados
                        'address' => "Col. " . fake('es_ES')->streetName() . ", " . $township,
                        'phone_number' => fake()->numerify('####-####'),
                        'department' => $department,
                        'township' => $township,
                        'type_price_id' => rand(1, 3), // Asumiendo que hay 3 tipos de precios
                    ]);

                    Location::create([
                        'latitude' => $data['lat'] + (rand(-10, 10) / 1000),
                        'longitude' => $data['lng'] + (rand(-10, 10) / 1000),
                        'model_id' => $client->id,
                        'model_type' => Client::class,
                    ]);
                }
            }
        }
    }
}