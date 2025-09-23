<?php

namespace App\Enums;

enum ReconciliationStatusEnum: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case WITH_DIFFERENCES = 'with_differences';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::COMPLETED => 'Completado',
            self::WITH_DIFFERENCES => 'Con Diferencias',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::COMPLETED => 'success',
            self::WITH_DIFFERENCES => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::PENDING => 'heroicon-o-clock',
            self::COMPLETED => 'heroicon-o-check-circle',
            self::WITH_DIFFERENCES => 'heroicon-o-exclamation-circle',
        };
    }

    public function getLabelWithIcon(): string
    {
        return $this->getIcon() . ' ' . $this->getLabel();
    }
}