<?php

namespace App\Filament\Widgets;

use App\Models\FinishedProductInventory;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class StockAlertsWidget extends BaseWidget
{
    protected static ?string $heading = '🚨 Alertas de Stock Crítico';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                FinishedProductInventory::query()
                    ->whereColumn('stock', '<=', 'min_stock')
                    ->with(['product'])
                    ->orderBy('stock', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('product.code')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),
                    
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
                    
                Tables\Columns\TextColumn::make('product.unit.name')
                    ->label('Unidad')
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product.category_id')
                    ->label('Categoría')
                    ->relationship('product.category', 'name')
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('restock')
                    ->label('Reabastecer')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->url(fn ($record) => route('filament.admin.resources.finished-product-inventories.edit', $record)),
            ])
            ->emptyStateHeading('¡Excelente! No hay productos con stock crítico')
            ->emptyStateDescription('Todos los productos tienen stock suficiente según sus niveles mínimos.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->striped()
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10);
    }
}