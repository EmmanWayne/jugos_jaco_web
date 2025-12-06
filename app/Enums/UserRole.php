<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPERADMIN = 'superadministrador';
    case ADMIN = 'administrador';
    case EMPLOYED = 'empleados';
    case CASHEER = 'cajero';

    public static function getAllowedRoles(): array
    {
        return [self::SUPERADMIN->value, self::ADMIN->value, self::EMPLOYED->value];
    }

    public static function getWidgetsMap(): array
    {
        return [
            self::SUPERADMIN->value => [
                \App\Filament\Widgets\StatsOverview::class,
                \App\Filament\Widgets\ClientsPerEmployeeWidget::class,
                \App\Filament\Widgets\StockAlertsWidget::class,
                \App\Filament\Widgets\RawMaterialStockAlertsWidget::class,
                \App\Filament\Widgets\SalesRankingWidget::class,
                \App\Filament\Widgets\AccountsReceivableWidget::class,
                \App\Filament\Widgets\AccountWidget::class,
                \App\Filament\Widgets\FilamentInfoWidget::class,
            ],
            self::ADMIN->value => [
                \App\Filament\Widgets\StatsOverview::class,
                \App\Filament\Widgets\ClientsPerEmployeeWidget::class,
                \App\Filament\Widgets\StockAlertsWidget::class,
                \App\Filament\Widgets\RawMaterialStockAlertsWidget::class,
                \App\Filament\Widgets\SalesRankingWidget::class,
                \App\Filament\Widgets\AccountsReceivableWidget::class,
                \App\Filament\Widgets\AccountWidget::class,
            ],
            self::CASHEER->value => [
                \App\Filament\Widgets\StatsOverview::class,
                \App\Filament\Widgets\StockAlertsWidget::class,
                \App\Filament\Widgets\RawMaterialStockAlertsWidget::class,
            ],
        ];
    }

    public static function getAllowedWidgetsForRoles(array $roles): array
    {
        $map = self::getWidgetsMap();
        $allowed = [];
        foreach ($roles as $role) {
            if (isset($map[$role])) {
                $allowed = array_merge($allowed, $map[$role]);
            }
        }
        return array_values(array_unique($allowed));
    }

    public static function canRoleViewWidget(string $role, string $widgetClass): bool
    {
        $map = self::getWidgetsMap();
        return isset($map[$role]) && in_array($widgetClass, $map[$role], true);
    }

    public static function canUserViewWidget(?\App\Models\User $user, string $widgetClass): bool
    {
        if (! $user) return false;
        $roles = $user->getRoleNames()->toArray();
        foreach ($roles as $role) {
            if (self::canRoleViewWidget($role, $widgetClass)) {
                return true;
            }
        }
        return false;
    }
}
