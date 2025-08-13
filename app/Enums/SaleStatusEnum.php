<?php

namespace App\Enums;

enum SaleStatusEnum: string
{
    case DRAFT = 'draft';
    case CONFIRMED = 'confirmed';
    case PARTIALLY_PAID = 'partially_paid';
    case PAID = 'paid';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => 'Borrador',
            self::CONFIRMED => 'Confirmada',
            self::PARTIALLY_PAID => 'Parcialmente Pagada',
            self::PAID => 'Pagada',
            self::CANCELLED => 'Cancelada',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::CONFIRMED => 'info',
            self::PARTIALLY_PAID => 'warning',
            self::PAID => 'success',
            self::CANCELLED => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::DRAFT => 'heroicon-o-document',
            self::CONFIRMED => 'heroicon-o-check-circle',
            self::PARTIALLY_PAID => 'heroicon-o-clock',
            self::PAID => 'heroicon-o-check-badge',
            self::CANCELLED => 'heroicon-o-x-circle',
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

    public static function getSelectOptions(): array
    {
        return collect(self::cases())
            ->map(fn($case) => [
                'value' => $case->value,
                'label' => $case->getLabel(),
                'color' => $case->getColor(),
                'icon' => $case->getIcon(),
            ])
            ->toArray();
    }

    public function canBeModified(): bool
    {
        return $this === self::DRAFT;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this, [self::DRAFT, self::CONFIRMED]);
    }

    public function canBeConfirmed(): bool
    {
        return $this === self::DRAFT;
    }

    public function canReceivePayments(): bool
    {
        return in_array($this, [self::CONFIRMED, self::PARTIALLY_PAID]);
    }
}
