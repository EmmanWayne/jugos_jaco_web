<?php

namespace App\Enums;

enum TypeInventoryManagementEnum: string
{
    case ENTRADA = 'entrada';
    case SALIDA = 'salida';
    case DANADO = 'dañado';
    case DEVOLUCION = 'devolución';

    public function getLabel(): string
    {
        return match ($this) {
            self::ENTRADA => 'Entrada',
            self::SALIDA => 'Salida',
            self::DANADO => 'Dañado',
            self::DEVOLUCION => 'Devolución',
        };
    }

    public static function getOptions(): array
    {
        return [
            self::ENTRADA->value => 'Entrada',
            self::SALIDA->value => 'Salida',
            self::DANADO->value => 'Dañado',
            self::DEVOLUCION->value => 'Devolución',
        ];
    }

    public static function getColor(string $type): string
    {
        return match ($type) {
            self::ENTRADA->value => 'success',
            self::SALIDA->value => 'warning',
            self::DANADO->value => 'danger',
            self::DEVOLUCION->value => 'info',
            default => 'gray',
        };
    }
}
