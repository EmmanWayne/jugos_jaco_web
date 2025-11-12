<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\AccountReceivable;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;

class AccountsReceivableWidget extends BaseWidget
{
    protected static ?string $heading = '💰 Cartera de Cobranza por Empleado';
    protected static ?int $sort = 4;
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
                Employee::query()
                    ->select([
                        'employees.id',
                        'employees.first_name',
                        'employees.last_name',
                        'employees.created_at',
                        'employees.updated_at',
                        DB::raw('COALESCE(COUNT(CASE WHEN account_receivables.status = "pending" THEN 1 END), 0) as pending_accounts'),
                        DB::raw('COALESCE(SUM(CASE WHEN account_receivables.status = "pending" THEN account_receivables.remaining_balance ELSE 0 END), 0) as total_pending'),
                        DB::raw('COALESCE(AVG(CASE WHEN account_receivables.status = "pending" THEN account_receivables.remaining_balance END), 0) as avg_pending'),
                        DB::raw('COALESCE(COUNT(CASE WHEN account_receivables.due_date < CURDATE() AND account_receivables.status = "pending" THEN 1 END), 0) as overdue_accounts'),
                        DB::raw('COALESCE(SUM(CASE WHEN account_receivables.due_date < CURDATE() AND account_receivables.status = "pending" THEN account_receivables.remaining_balance ELSE 0 END), 0) as overdue_amount'),
                    ])
                    ->leftJoin('sales', 'employees.id', '=', 'sales.employee_id')
                    ->leftJoin('account_receivables', 'sales.id', '=', 'account_receivables.sales_id')
                    ->groupBy('employees.id', 'employees.first_name', 'employees.last_name', 'employees.created_at', 'employees.updated_at')
                    ->orderBy('total_pending', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Empleado')
                    ->getStateUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('pending_accounts')
                    ->label('Cuentas Pendientes')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state == 0 => 'success',
                        $state <= 5 => 'warning',
                        default => 'danger'
                    })
                    ->formatStateUsing(fn ($state) => number_format($state, 0)),
                    
                Tables\Columns\TextColumn::make('total_pending')
                    ->label('Monto Total Pendiente')
                    ->sortable()
                    ->money('HNL')
                    ->weight('bold')
                    ->color(fn ($state) => match(true) {
                        $state == 0 => 'success',
                        $state <= 50000 => 'warning',
                        default => 'danger'
                    }),
                    
                Tables\Columns\TextColumn::make('avg_pending')
                    ->label('Promedio por Cuenta')
                    ->sortable()
                    ->money('HNL')
                    ->color('info'),
                    
                Tables\Columns\TextColumn::make('overdue_accounts')
                    ->label('Cuentas Vencidas')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                    ->formatStateUsing(fn ($state) => number_format($state, 0)),
                    
                Tables\Columns\TextColumn::make('overdue_amount')
                    ->label('Monto Vencido')
                    ->sortable()
                    ->money('HNL')
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
                    
                Tables\Columns\TextColumn::make('collection_priority')
                    ->label('Prioridad')
                    ->getStateUsing(function ($record) {
                        if ($record->overdue_amount > 100000) return 'Crítica';
                        if ($record->overdue_amount > 50000) return 'Alta';
                        if ($record->total_pending > 0) return 'Media';
                        return 'Baja';
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'Crítica' => 'danger',
                        'Alta' => 'warning',
                        'Media' => 'info',
                        'Baja' => 'success',
                    }),
            ])
            ->filters([
                Tables\Filters\Filter::make('has_pending')
                    ->label('Solo con cuentas pendientes')
                    ->query(fn (Builder $query): Builder => $query->having('total_pending', '>', 0))
                    ->default(),
                    
                Tables\Filters\Filter::make('overdue_only')
                    ->label('Solo cuentas vencidas')
                    ->query(fn (Builder $query): Builder => $query->having('overdue_accounts', '>', 0)),
            ])
            ->actions([
                Tables\Actions\Action::make('manage_accounts')
                    ->label('Gestionar Cuentas')
                    ->icon('heroicon-o-banknotes')
                    ->color('primary')
                    ->url(fn ($record) => route('filament.admin.resources.account-receivables.index', [
                        'tableFilters[employee_id][value]' => $record->id
                    ])),
            ])
            ->emptyStateHeading('¡Excelente! No hay cuentas por cobrar pendientes')
            ->emptyStateDescription('Todos los empleados tienen sus carteras de cobranza al día.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->striped()
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10);
    }
}
