<?php

namespace App\Enums;

enum PaymentTermEnum: string
{
    case CASH = 'cash';
    case CREDIT = 'credit';

    public function getLabel(): string
    {
        return match ($this) {
            self::CASH => 'Contado',
            self::CREDIT => 'Crédito',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::CASH => '💵',
            self::CREDIT => '📅',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::CASH => 'success',
            self::CREDIT => 'warning',
        };
    }

    public static function getOptions(): array
    {
        return [
            self::CASH->value => self::CASH->getLabel(),
            self::CREDIT->value => self::CREDIT->getLabel(),
        ];
    }

    public static function getIcons(): array
    {
        return [
            self::CASH->value => self::CASH->getIcon(),
            self::CREDIT->value => self::CREDIT->getIcon(),
        ];
    }

    public static function getColors(): array
    {
        return [
            self::CASH->value => self::CASH->getColor(),
            self::CREDIT->value => self::CREDIT->getColor(),
        ];
    }
}
