<?php

namespace App\Filament\Widgets;

use App\Models\RawMaterialsInventory;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;

class RawMaterialStockAlertsWidget extends BaseWidget
{
    protected static ?string $heading = '🚨 Alertas de Materia Prima';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected static bool $isLazy = true;

    public static function canView(): bool
    {
        $user = Auth::user();
        return UserRole::canUserViewWidget($user, static::class);
    }

    public function mount(): void
    {
        if (! UserRole::canUserViewWidget(Auth::user(), static::class)) {
            abort(403);
        }
    }
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                RawMaterialsInventory::query()
                    ->whereColumn('stock', '<=', 'min_stock')
                    ->orderBy('stock', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Materia Prima')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stock Actual')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($record) => $record->stock <= $record->min_stock ? 'danger' : 'warning'),

                Tables\Columns\TextColumn::make('min_stock')
                    ->label('Stock Mínimo')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('faltante')
                    ->label('Faltante')
                    ->getStateUsing(fn ($record) => max(0, $record->min_stock - $record->stock))
                    ->numeric()
                    ->color('danger'),
                    
                Tables\Columns\TextColumn::make('unit_type')
                    ->label('Unidad')
                    ->badge()
                    ->color('gray'),
            ])
            ->actions([
                Tables\Actions\Action::make('restock')
                    ->label('Reabastecer')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->url(fn ($record) => route('filament.admin.resources.raw-materials-inventories.edit', $record)),
            ])
            ->emptyStateHeading('¡Excelente! No hay materia prima con stock crítico')
            ->emptyStateDescription('Todos los insumos tienen stock suficiente según sus niveles mínimos.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->striped()
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
    }
}
