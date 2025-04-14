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
