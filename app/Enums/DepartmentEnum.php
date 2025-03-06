<?php

namespace App\Enums;

enum DepartmentEnum: string
{
    case ATLANTIDA = 'Atlántida';
    case COLON = 'Colón';
    case COMAYAGUA = 'Comayagua';
    case COPAN = 'Copán';
    case CORTES = 'Cortés';
    case CHOLUTECA = 'Choluteca';
    case EL_PARAISO = 'El Paraíso';
    case FRANCISCO_MORAZAN = 'Francisco Morazán';
    case GRACIAS_A_DIOS = 'Gracias a Dios';
    case INTIBUCA = 'Intibucá';
    case ISLAS_DE_LA_BAHIA = 'Islas de la Bahía';
    case LA_PAZ = 'La Paz';
    case LEMPIRA = 'Lempira';
    case OCOTEPEQUE = 'Ocotepeque';
    case OLANCHO = 'Olancho';
    case SANTA_BARBARA = 'Santa Bárbara';
    case VALLE = 'Valle';
    case YORO = 'Yoro';

    public static function toArray(): array
    {
        return collect(self::cases())->pluck('value', 'value')->toArray();
    }
}
