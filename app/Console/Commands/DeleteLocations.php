<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\Employee;
use Illuminate\Console\Command;

class DeleteLocations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Commando para eliminar las ubicaciones de los empleados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Employee::whereHas('user')->each(function ($employee) {
            if ($employee->user->hasRole(UserRole::CASHEER)) return;

            $this->info("Eliminando ubicaciones del empleado {$employee->full_name}");
            $employee->locations()->delete();
        });

        $this->info('Ubicaciones eliminadas correctamente');
    }
}
