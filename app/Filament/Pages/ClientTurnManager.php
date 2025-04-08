<?php

namespace App\Filament\Pages;

use App\Models\Client;
use App\Models\Employee;
use App\Enums\VisitDayEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Colors\Color;

class ClientTurnManager extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $navigationLabel = 'Turnos de Visita';
    protected static ?string $title = 'Gestión de Turnos de Visita';
    protected static ?string $navigationGroup = 'Clientes';
    protected static string $view = 'filament.pages.client-turn-manager';

    protected function getDefaultVisitDay(): string
    {
        $dayMap = [
            1 => VisitDayEnum::LUNES->value,
            2 => VisitDayEnum::MARTES->value,
            3 => VisitDayEnum::MIERCOLES->value,
            4 => VisitDayEnum::JUEVES->value,
            5 => VisitDayEnum::VIERNES->value,
            6 => VisitDayEnum::SABADO->value,
            7 => VisitDayEnum::LUNES->value, // Domingo muestra Lunes
        ];

        // Usar la zona horaria de Honduras
        $today = Carbon::now('America/Tegucigalpa');
        return $dayMap[$today->dayOfWeek];
    }

    public function table(Table $table): Table
    {
        $currentDay = $this->getDefaultVisitDay();

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
}
