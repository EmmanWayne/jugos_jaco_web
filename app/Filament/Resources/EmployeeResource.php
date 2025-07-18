<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use App\Models\Branch;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationGroup = 'Administración';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información del empleado')  // Título de la sección
                    ->description('En esta sección se registra la información personal del empleado.') // Descripción
                    ->schema([
                        Section::make('')
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('first_name')
                                    ->label('Nombres')
                                    ->required()
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('last_name')
                                    ->label('Apellidos')
                                    ->required()
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('phone_number')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->required()
                                    ->maxLength(15),
                                Forms\Components\TextInput::make('identity')
                                    ->label('Identidad')
                                    ->required()
                                    ->maxLength(13)
                                    ->unique(ignoreRecord: true)
                                    ->numeric()
                                    ->afterStateUpdated(fn($state, callable $set) => self::validateIdentity($state)),
                            ]),

                        Section::make('')
                            ->columns(1)
                            ->schema([
                                Forms\Components\Select::make('branch_id')
                                    ->label('Sucursal')
                                    ->relationship(
                                        name: 'branch',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn($query) => $query->orderBy('name')
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nombre')
                                            ->required()
                                            ->maxLength(50),
                                        Forms\Components\TextInput::make('phone_number')
                                            ->label('Teléfono')
                                            ->tel()
                                            ->required()
                                            ->maxLength(15),
                                        Forms\Components\TextInput::make('address')
                                            ->label('Dirección')
                                            ->required()
                                            ->maxLength(120),
                                    ])
                                    ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                        return $action
                                            ->modalHeading('Crear nueva sucursal')
                                            ->modalButton('Crear sucursal')
                                            ->modalWidth('lg');
                                    }),
                            ]),

                        Section::make('')
                            ->columns(1)
                            ->schema([
                                Forms\Components\TextInput::make('address')
                                    ->label('Dirección')
                                    ->required()
                                    ->maxLength(120),
                            ]),
                    ]),
            ]);
    }


    /**
     * Valida la identidad y lanza una notificación si ya existe.
     */
    protected static function validateIdentity($identity)
    {
        $employee_id = request()->route('record');
        if (\App\Models\Employee::where([['identity', $identity], ['id', '!=', $employee_id]])->exists()) {
            Notification::make()
                ->title('¡Atención!')
                ->body('La identidad ya está registrada en el sistema.')
                ->danger()
                ->send();
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nombre Completo')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(query: function ($query, $direction) {
                        return $query
                            ->orderBy('first_name', $direction)
                            ->orderBy('last_name', $direction);
                    }),
                Tables\Columns\TextColumn::make('identity')
                    ->label('Identidad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Teléfono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Sucursal')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Dirección')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Fecha de edición')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }


    public static function getNavigationLabel(): string
    {
        return 'Empleados';
    }

    public static function getModelLabel(): string
    {
        return 'Empleado';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Empleados';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-identification';
    }

    public static function getNavigationSort(): int
    {
        return 1;
    }
}
