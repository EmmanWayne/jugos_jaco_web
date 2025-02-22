<?php

namespace App\Filament\Resources;

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
                            ->columns(4)
                            ->schema([
                                Forms\Components\Select::make('department')
                                    ->label('Departamento')
                                    ->options([
                                        'Atlántida' => 'Atlántida',
                                        'Colón' => 'Colón',
                                        'Comayagua' => 'Comayagua',
                                        'Copán' => 'Copán',
                                        'Cortés' => 'Cortés',
                                        'Choluteca' => 'Choluteca',
                                        'El Paraíso' => 'El Paraíso',
                                        'Francisco Morazán' => 'Francisco Morazán',
                                        'Gracias a Dios' => 'Gracias a Dios',
                                        'Intibucá' => 'Intibucá',
                                        'Islas de la Bahía' => 'Islas de la Bahía',
                                        'La Paz' => 'La Paz',
                                        'Lempira' => 'Lempira',
                                        'Ocotepeque' => 'Ocotepeque',
                                        'Olancho' => 'Olancho',
                                        'Santa Bárbara' => 'Santa Bárbara',
                                        'Valle' => 'Valle',
                                        'Yoro' => 'Yoro',
                                    ])
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\TextInput::make('township')
                                    ->label('Municipio')
                                    ->required()
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('latitude')
                                    ->label('Latitud')
                                    ->numeric()
                                    ->default(null),
                                Forms\Components\TextInput::make('longitude')
                                    ->label('Longitud')
                                    ->numeric()
                                    ->default(null),
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
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Nombres')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Apellidos')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Teléfono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Dirección')
                    ->searchable(),
                Tables\Columns\TextColumn::make('department')
                    ->label('Departamento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('township')
                    ->label('Municipio')
                    ->searchable(),
                Tables\Columns\TextColumn::make('latitude')
                    ->label('Latitud')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('longitude')
                    ->label('Longitud')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Empleado asignado')
                    ->sortable(),
                Tables\Columns\TextColumn::make('typePrice.name')
                    ->label('Tipo de precio')
                    ->sortable(),
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
        return 'heroicon-o-users';
    }

    public static function getNavigationSort(): int
    {
        return 3;
    }
}
