<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientTransferService
{
    public function transferClients(
        Employee $sourceEmployee,
        Employee $targetEmployee,
        array $clientIds = [],
        bool $transferAll = false
    ): array {
        try {
            DB::beginTransaction();

            // Validaciones
            if ($sourceEmployee->id === $targetEmployee->id) {
                throw new \InvalidArgumentException('El empleado origen y destino no pueden ser el mismo.');
            }

            // Determinar qué clientes transferir
            if ($transferAll) {
                $clientsToTransfer = $sourceEmployee->clients()->get();
            } else {
                if (empty($clientIds)) {
                    throw new \InvalidArgumentException('Debe seleccionar al menos un cliente para transferir.');
                }
                $clientsToTransfer = Client::whereIn('id', $clientIds)
                    ->where('employee_id', $sourceEmployee->id)
                    ->get();
            }

            if ($clientsToTransfer->isEmpty()) {
                throw new \RuntimeException('No se encontraron clientes válidos para transferir.');
            }

            // Verificar que todos los clientes pertenecen al empleado origen
            $invalidClients = $clientsToTransfer->filter(function ($client) use ($sourceEmployee) {
                return $client->employee_id !== $sourceEmployee->id;
            });

            if ($invalidClients->isNotEmpty()) {
                throw new \RuntimeException('Algunos clientes seleccionados no pertenecen al empleado origen.');
            }

            // Realizar la transferencia
            $transferredCount = $clientsToTransfer->count();
            $clientsTransferredIds = $clientsToTransfer->pluck('id')->toArray();
            
            Client::whereIn('id', $clientsTransferredIds)
                ->update([
                    'employee_id' => $targetEmployee->id,
                    'updated_at' => now(),
                ]);

            // Crear el log de transferencia
            $logData = [
                'source_employee_id' => $sourceEmployee->id,
                'source_employee_name' => $sourceEmployee->full_name,
                'target_employee_id' => $targetEmployee->id,
                'target_employee_name' => $targetEmployee->full_name,
                'clients_transferred' => $transferredCount,
                'clients_ids' => $clientsTransferredIds,
                'transfer_all' => $transferAll,
                'transferred_at' => now()->toDateTimeString(),
                'user_id' => Auth::id(),
            ];

            Log::info('Transferencia de clientes realizada exitosamente', $logData);

            DB::commit();

            return [
                'success' => true,
                'message' => "Se transfirieron {$transferredCount} cliente(s) de {$sourceEmployee->full_name} a {$targetEmployee->full_name}.",
                'transferred_count' => $transferredCount,
                'clients_transferred' => $clientsToTransfer->toArray(),
                'log_data' => $logData,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            $errorData = [
                'error' => $e->getMessage(),
                'source_employee_id' => $sourceEmployee->id ?? null,
                'target_employee_id' => $targetEmployee->id ?? null,
                'transfer_all' => $transferAll ?? false,
                'client_ids' => $clientIds ?? [],
                'user_id' => Auth::id(),
            ];

            Log::error('Error en transferencia de clientes', $errorData);

            return [
                'success' => false,
                'message' => 'Error al transferir los clientes: ' . $e->getMessage(),
                'error' => $e->getMessage(),
                'error_data' => $errorData,
            ];
        }
    }

    public function getTransferStats(Employee $employee): array
    {
        $clientsCount = $employee->clients()->count();
        $recentClientsCount = $employee->clients()
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        return [
            'total_clients' => $clientsCount,
            'recent_clients' => $recentClientsCount,
            'has_clients' => $clientsCount > 0,
            'can_transfer' => $clientsCount > 0,
        ];
    }

    public function getAvailableTargetEmployees(Employee $sourceEmployee): Collection
    {
        return Employee::where('id', '!=', $sourceEmployee->id)
            ->with('branch')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
    }

    public function validateTransfer(Employee $sourceEmployee, Employee $targetEmployee, array $clientIds = [], bool $transferAll = false): array
    {
        $errors = [];

        if ($sourceEmployee->id === $targetEmployee->id) {
            $errors[] = 'El empleado origen y destino no pueden ser el mismo.';
        }

        if (!$transferAll && empty($clientIds)) {
            $errors[] = 'Debe seleccionar al menos un cliente para transferir.';
        }

        if ($transferAll) {
            $clientsCount = $sourceEmployee->clients()->count();
            if ($clientsCount === 0) {
                $errors[] = 'El empleado origen no tiene clientes para transferir.';
            }
        } else {
            $validClients = Client::whereIn('id', $clientIds)
                ->where('employee_id', $sourceEmployee->id)
                ->count();
            
            if ($validClients !== count($clientIds)) {
                $errors[] = 'Algunos clientes seleccionados no son válidos o no pertenecen al empleado origen.';
            }
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}
