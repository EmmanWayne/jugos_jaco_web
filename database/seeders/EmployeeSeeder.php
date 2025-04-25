<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Employee::create([
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'identity' => '1234567890123',
            'phone_number' => '123456789',
            'address' => 'Calle 123',
            'branch_id' => Branch::first()->id
        ]);
    }
}
