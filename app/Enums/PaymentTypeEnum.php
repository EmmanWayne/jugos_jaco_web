<?php

namespace App\Enums;

enum PaymentTypeEnum: string
{
    case CASH = 'cash';
    case CREDIT = 'credit';
    case DEPOSIT = 'deposit';
    case CARD = 'card';

    public function getLabel(): string
    {
        return match ($this) {
            self::CASH => 'Efectivo',
            self::CREDIT => 'Crédito',
            self::DEPOSIT => 'Depósito',
            self::CARD => 'Tarjeta',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::CASH => '💵',
            self::CREDIT => '📅',
            self::DEPOSIT => '🏦',
            self::CARD => '💳',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::CASH => 'success',
            self::CREDIT => 'warning',
            self::DEPOSIT => 'info',
            self::CARD => 'primary',
        };
    }

    public function getLabelWithIcon(): string
    {
        return $this->getIcon() . ' ' . $this->getLabel();
    }

    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getLabel();
        }
        return $options;
    }

    public function requiresDueDate(): bool
    {
        return $this === self::CREDIT;
    }

    public function requiresDeposit(): bool
    {
        return $this === self::DEPOSIT;
    }

    public function requiresCashAmount(): bool
    {
        return in_array($this, [self::CASH, self::DEPOSIT]);
    }
}
