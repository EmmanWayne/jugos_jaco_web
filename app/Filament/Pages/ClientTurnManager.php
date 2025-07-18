<?php

namespace App\Filament\Pages;

use App\Models\Client;
use App\Models\Employee;
use App\Enums\VisitDayEnum;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;

class ClientTurnManager extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $navigationLabel = 'Turnos De Visita';
    protected static ?string $title = 'Gestión de Turnos de Visita';
    protected static ?string $navigationGroup = 'Clientes';
    protected static string $view = 'filament.pages.client-turn-manager';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Client::query()
                    ->with(['employee'])
                    ->when(
                        request()->filled('tableFilters.visit_day'),
                        fn($query) => $query->where('visit_day', request()->input('tableFilters.visit_day')),

                    )
                    ->when(
                        request()->filled('tableFilters.employee_id'),
                        fn($query) => $query->where('employee_id', request()->input('tableFilters.employee_id'))
                    )
                    ->orderBy('position')
            )
            ->reorderable('position', true, 'Reordenar los turnos')
            ->columns([
                TextColumn::make('position')
                    ->label('Turno')
                    ->badge()
                    ->color('warning')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('business_name')
                    ->label('Negocio')
                    ->searchable()
                    ->description(fn(Client $record): string => "Cliente: {$record->first_name} {$record->last_name}")
                    ->wrap(),
                TextColumn::make('employee.first_name')
                    ->label('Empleado')
                    ->searchable()
                    ->description(fn(Client $record): string => "Tel: {$record->phone_number}")
                    ->icon('heroicon-o-user'),
                TextColumn::make('visit_day')
                    ->label('Día de Visita')
                    ->badge()
                    ->color('success')
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('employee_id')
                    ->label('Filtrar por Empleado')
                    ->options(
                        fn() => Employee::query()
                            ->orderBy('first_name')
                            ->pluck('first_name', 'id')
                            ->toArray()
                    )
                    ->searchable()
                    ->placeholder('Seleccionar Empleado')
                    ->preload(),
                SelectFilter::make('visit_day')
                    ->label('Filtrar por Día')
                    ->options(VisitDayEnum::class)
                    ->placeholder('Seleccionar Día')
                    ->preload()
            ])
            ->striped();
    }

    public function reorder(array $items): void
    {
        foreach ($items as $item) {
            Client::find($item['id'])->update(['position' => $item['order']]);
        }
    }

    protected function hasTableColumnSearchIndividually(): bool
    {
        return true;
    }

    public static function getNavigationSort(): int
    {
        return 2;
    }
    
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
