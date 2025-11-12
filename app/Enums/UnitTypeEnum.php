<?php

namespace App\Enums;

enum UnitTypeEnum: string
{
    case LITER = 'Litro';
    case GRAM = 'Gramo';
    case KILOGRAM = 'Kilogramo';
    case UNIT = 'Unidad';
    case MILLILITER = 'Mililitro';
    case POUND = 'Libra';
    case BALE = 'Fardo';
    case BOX = 'Caja';
    case PACKAGE = 'Paquete';
    case SACK = 'Saco';

    public static function getValues(): array
    {
        return collect(self::cases())->pluck('value', 'value')->toArray();
    }

    public static function getLabel(string $value): string
    {
        return self::from($value)->value;
    }
}
