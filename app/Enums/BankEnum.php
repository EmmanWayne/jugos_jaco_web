<?php

namespace App\Enums;

enum BankEnum: string
{
    case ATLANTIDA = 'Banco Atlántida';
    case FICOHSA = 'Ficohsa';
    case BANPAIS = 'BanPaís';
    case BAC_CREDOMATIC = 'Bac Credomatic';
    case OCCIDENTE = 'Banco de Occidente';
    case BANRURAL = 'Banrural';
    case BANADESA = 'Banadesa';
    case DA_VIVIENDA = 'Davivienda';
    case OTRO = 'Otro';

    public function getColor(): string
    {
        return match ($this) {
            self::ATLANTIDA => 'danger',
            self::FICOHSA => 'info',
            self::BANPAIS => 'warning',
            self::BAC_CREDOMATIC => 'danger',
            self::OCCIDENTE => 'success',
            self::BANRURAL => 'primary',
            self::BANADESA => 'success',
            self::DA_VIVIENDA => 'danger',
            default => 'light',
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::ATLANTIDA => 'Banco Atlántida',
            self::FICOHSA => 'Ficohsa',
            self::BANPAIS => 'BanPaís',
            self::BAC_CREDOMATIC => 'Bac Credomatic',
            self::OCCIDENTE => 'Banco de Occidente',
            self::BANRURAL => 'Banrural',
            self::BANADESA => 'Banadesa',
            self::DA_VIVIENDA => 'Davivienda',
            default => 'Otro',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->pluck('value', 'value')->toArray();
    }

    public static function bankColor(string $bank): string
    {
        return match ($bank) {
            self::ATLANTIDA->name => 'danger',
            self::FICOHSA->name => 'info',
            self::BANPAIS->name => 'warning',
            self::BAC_CREDOMATIC->name => 'danger',
            self::OCCIDENTE->name => 'success',
            self::BANRURAL->name => 'primary',
            self::BANADESA->name => 'success',
            self::DA_VIVIENDA->name => 'danger',
            default => 'light',
        };
    }
}
