<?php

namespace App\Filament\Resources;

use App\Enums\DepartmentEnum;
use App\Enums\MunicipalityEnum;
use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use App\Models\Employee;
use App\Models\TypePrice;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    public static function form(Form $form): Form
    {
        return $form


            ->schema([
                Section::make('Información del cliente')  // Título de la sección
                    ->description('En esta sección se registra la información del cliente.') // Descripción
                    ->schema([
                        Section::make('')
                            ->columns(3)
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
                            ]),

                        Section::make('')
                            ->columns(1)
                            ->schema([
                                Forms\Components\TextInput::make('address')
                                    ->label('Dirección')
                                    ->required()
                                    ->maxLength(120),
                            ]),
                        Section::make('')
                            ->columns(2)
                            ->schema([
                                Forms\Components\Select::make('department')
                                    ->label('Departamento')
                                    ->searchable()
                                    ->options(DepartmentEnum::toArray())
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        // Limpiamos la selección de municipio cuando cambia el departamento
                                        $set('municipality', null);
                                    })
                                    ->required(),

                                Forms\Components\Select::make('township')
                                    ->label('Municipio')
                                    ->options(function (callable $get) {
                                        $department = $get('department');
                                        if (!$department) {
                                            return [];
                                        }

                                        return collect(MunicipalityEnum::getByDepartment(DepartmentEnum::from($department)))
                                            ->mapWithKeys(fn($municipality) => [$municipality => $municipality]);
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),

                        Section::make('')
                            ->columns(2)
                            ->schema([
                                Forms\Components\Select::make('employee_id')
                                    ->label('Empleado asignado')
                                    ->options(Employee::selectRaw("CONCAT(first_name, ' ', last_name) as name, id")->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\Select::make('type_price_id')
                                    ->label('Tipo de Precio')
                                    ->options(TypePrice::query()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nombre del Tipo de Precio')
                                            ->required(),
                                    ])
                                    ->createOptionUsing(fn(array $data) => TypePrice::create($data)->id) // Guarda el nuevo tipo de precio
                                    ->required(),
                            ]),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nombre Completo')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Teléfono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Empleado asignado')
                    ->sortable(),
                Tables\Columns\TextColumn::make('typePrice.name')
                    ->label('Tipo de precio')
                    ->sortable(),
                Tables\Columns\TextColumn::make('department')
                    ->label('Departamento')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('township')
                    ->label('Municipio')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'view' => Pages\ViewClient::route('/{record}'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Clientes';
    }

    public static function getModelLabel(): string
    {
        return 'Cliente';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Clientes';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-user-group';
    }

    public static function getNavigationSort(): int
    {
        return 3;
    }

    public static function create(array $data)
    {
        // Guardar el cliente primero sin latitude y longitude
        $client = Client::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone_number' => $data['phone_number'],
            'address' => $data['address'],
            'department' => $data['department'],
            'township' => $data['township'],
            'employee_id' => $data['employee_id'],
            'type_price_id' => $data['type_price_id'],
        ]);

        // Guardar la ubicación polimórficamente
        $client->location()->create([
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'model_id' => $client->id,
            'model' => Client::class,
        ]);

        return $client;
    }
}
