<?php

namespace App\Services;

use App\Enums\TypeInventoryManagementEnum;
use App\Models\ManagementInventory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManagementInventoryService
{
    /**
     * Procesa un movimiento de inventario en base al tipo especificado
     *
     * @param Model $model El modelo al que se aplica el movimiento
     * @param float $quantity La cantidad del movimiento
     * @param string $type El tipo de movimiento (entrada, salida, dañado, devolución)
     * @param string $description Descripción del movimiento
     * @param int|null $referenceId ID de referencia opcional
     * @return ManagementInventory El registro creado
     * @throws \InvalidArgumentException Si el tipo de movimiento no es válido
     */
    public function processMovement(
        Model $model,
        float $quantity,
        string $type,
        string $description,
        ?int $referenceId = null
    ): ManagementInventory {
        if ($quantity <= 0) throw new \InvalidArgumentException('La cantidad debe ser mayor que cero');

        return match ($type) {
            TypeInventoryManagementEnum::ENTRADA->value => $this->registerEntry($model, $quantity, $description, $referenceId),
            TypeInventoryManagementEnum::SALIDA->value => $this->registerExit($model, $quantity, $description, $referenceId),
            TypeInventoryManagementEnum::DANADO->value => $this->registerDamaged($model, $quantity, $description, $referenceId),
            TypeInventoryManagementEnum::DEVOLUCION->value => $this->registerReturn($model, $quantity, $description, $referenceId),
            default => throw new \InvalidArgumentException('Tipo de movimiento de inventario no válido: ' . $type),
        };
    }

    /**
     * Registra un movimiento de inventario
     *
     * @param Model $model El modelo al que se aplica el movimiento (producto, materia prima, etc.)
     * @param float $quantity La cantidad del movimiento
     * @param TypeInventoryManagementEnum $type El tipo de movimiento (entrada, salida, dañado, devolución)
     * @param string $description Descripción del movimiento
     * @param int|null $referenceId ID de referencia opcional (ej: ID de factura, orden, etc.)
     * @return ManagementInventory El registro creado
     */
    protected function registerMovement(
        Model $model,
        float $quantity,
        TypeInventoryManagementEnum $type,
        string $description,
        ?int $referenceId = null
    ): ManagementInventory {
        if (!method_exists($model, 'movements')) throw new \RuntimeException('El modelo no tiene una relación "movements" definida');

        return $model->movements()->create([
            'description' => $description,
            'quantity' => $quantity,
            'type' => $type->value,
            'reference_id' => $referenceId,
            'created_by' => Auth::user()->name,
        ]);
    }

    /**
     * Registra una entrada de inventario
     *
     * @param Model $model El modelo al que se aplica la entrada
     * @param float $quantity La cantidad a incrementar
     * @param string $description Descripción de la entrada
     * @param int|null $referenceId ID de referencia opcional
     * @return ManagementInventory El registro creado
     */
    public function registerEntry(
        Model $model,
        float $quantity,
        string $description,
        ?int $referenceId = null
    ): ManagementInventory {
        return DB::transaction(function () use ($model, $quantity, $description, $referenceId) {
            $this->updateStock($model, $quantity);

            return $this->registerMovement(
                $model,
                $quantity,
                TypeInventoryManagementEnum::ENTRADA,
                $description,
                $referenceId
            );
        });
    }

    /**
     * Registra una salida de inventario
     *
     * @param Model $model El modelo al que se aplica la salida
     * @param float $quantity La cantidad a decrementar
     * @param string $description Descripción de la salida
     * @param int|null $referenceId ID de referencia opcional
     * @return ManagementInventory El registro creado
     */
    public function registerExit(
        Model $model,
        float $quantity,
        string $description,
        ?int $referenceId = null
    ): ManagementInventory {
        return DB::transaction(function () use ($model, $quantity, $description, $referenceId) {
            $this->checkAvailableStock($model, $quantity);

            $this->updateStock($model, -$quantity);

            return $this->registerMovement(
                $model,
                $quantity,
                TypeInventoryManagementEnum::SALIDA,
                $description,
                $referenceId
            );
        });
    }

    /**
     * Registra productos dañados en el inventario
     *
     * @param Model $model El modelo al que se aplica el daño
     * @param float $quantity La cantidad dañada
     * @param string $description Descripción del daño
     * @param int|null $referenceId ID de referencia opcional
     * @return ManagementInventory El registro creado
     */
    public function registerDamaged(
        Model $model,
        float $quantity,
        string $description,
        ?int $referenceId = null
    ): ManagementInventory {
        return DB::transaction(function () use ($model, $quantity, $description, $referenceId) {
            $this->checkAvailableStock($model, $quantity);

            $this->updateStock($model, -$quantity);

            return $this->registerMovement(
                $model,
                $quantity,
                TypeInventoryManagementEnum::DANADO,
                $description,
                $referenceId
            );
        });
    }

    /**
     * Registra una devolución en el inventario
     *
     * @param Model $model El modelo al que se aplica la devolución
     * @param float $quantity La cantidad devuelta
     * @param string $description Descripción de la devolución
     * @param int|null $referenceId ID de referencia opcional
     * @return ManagementInventory El registro creado
     */
    public function registerReturn(
        Model $model,
        float $quantity,
        string $description,
        ?int $referenceId = null
    ): ManagementInventory {
        return DB::transaction(function () use ($model, $quantity, $description, $referenceId) {
            $this->updateStock($model, $quantity);

            return $this->registerMovement(
                $model,
                $quantity,
                TypeInventoryManagementEnum::DEVOLUCION,
                $description,
                $referenceId
            );
        });
    }

    /**
     * Actualiza el stock de un modelo
     *
     * @param Model $model El modelo cuyo stock se actualizará
     * @param float $quantity La cantidad a añadir (positiva) o restar (negativa)
     * @return bool Si la actualización tuvo éxito
     */
    protected function updateStock(Model $model, float $quantity): bool
    {
        if (Arr::has($model, 'stock')) {
            $model->stock += $quantity;
            return $model->save();
        } 

        throw new \RuntimeException('El modelo no tiene una propiedad de stock o un método para actualizarlo');
    }

    /**
     * Verifica si hay suficiente stock disponible
     *
     * @param Model $model El modelo a verificar
     * @param float $quantity La cantidad necesaria
     * @return bool Verdadero si hay suficiente stock
     * @throws \RuntimeException Si no hay suficiente stock
     */
    protected function checkAvailableStock(Model $model, float $quantity): bool
    {
        $availableStock = 0;

        if (Arr::has($model, 'stock')) {
            $availableStock = $model->stock;
        } else {
            throw new \RuntimeException('No se puede determinar el stock disponible para este modelo');
        }

        if ($availableStock < $quantity) throw new \RuntimeException('No hay suficiente stock disponible. Stock: ' . $availableStock . ', Solicitado: ' . $quantity);

        return true;
    }
}
