<?php

namespace App\Enums;

enum UnitCategoryEnum: string
{
    case COUNT = 'count';
    case WEIGHT = 'weight';
    case VOLUME = 'volume';
    case LENGTH = 'length';
    case AREA = 'area';

    /**
     * Obtener el label traducido de la categoría
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::COUNT => 'Conteo',
            self::WEIGHT => 'Peso',
            self::VOLUME => 'Volumen',
            self::LENGTH => 'Longitud',
            self::AREA => 'Área',
        };
    }

    /**
     * Obtener todas las opciones como array para selects
     */
    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getLabel();
        }
        return $options;
    }

    /**
     * Obtener las categorías más comunes para productos
     */
    public static function getCommonOptions(): array
    {
        return [
            self::COUNT->value => self::COUNT->getLabel(),
            self::WEIGHT->value => self::WEIGHT->getLabel(),
            self::VOLUME->value => self::VOLUME->getLabel(),
        ];
    }
}
