<?php

namespace App\Enums;

enum ProductReturnTypeEnum: string
{
    case DAMAGED = 'damaged';
    case RETURNED = 'returned';

    public function getLabel(): string
    {
        return match($this) {
            self::DAMAGED => 'Producto Dañado',
            self::RETURNED => 'Retorno de Producto',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::DAMAGED => 'danger',
            self::RETURNED => 'warning',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}