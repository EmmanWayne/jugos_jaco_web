<?php

namespace App\Filament\Resources;

use App\Enums\DepartmentEnum;
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
                                    ->options(DepartmentEnum::toArray())
                                    ->enum(DepartmentEnum::class)
                                    ->placeholder('Seleccione un departamento')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('township', null); // Limpiar el municipio seleccionado
                                    }),
                                Forms\Components\Select::make('township')
                                    ->label('Municipio')
                                    ->options(fn(callable $get) => self::getAllMunicipalities()[$get('department')] ?? [])
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

    public static function getAllMunicipalities()
    {
        return [
            'Atlántida' => [
                'La Ceiba' => 'La Ceiba',
                'El Porvenir' => 'El Porvenir',
                'Tela' => 'Tela',
                'Jutiapa' => 'Jutiapa',
                'La Masica' => 'La Masica',
                'San Francisco' => 'San Francisco',
                'Arizona' => 'Arizona',
                'Esparta' => 'Esparta',
            ],
            'Choluteca' => [
                'Choluteca' => 'Choluteca',
                'Apacilagua' => 'Apacilagua',
                'Concepción de María' => 'Concepción de María',
                'Duyure' => 'Duyure',
                'El Corpus' => 'El Corpus',
                'El Triunfo' => 'El Triunfo',
                'Marcovia' => 'Marcovia',
                'Morolica' => 'Morolica',
                'Namasigüe' => 'Namasigüe',
                'Orocuina' => 'Orocuina',
                'Pespire' => 'Pespire',
                'San Antonio de Flores' => 'San Antonio de Flores',
                'San Isidro' => 'San Isidro',
                'San José' => 'San José',
                'San Marcos de Colón' => 'San Marcos de Colón',
                'Santa Ana de Yusguare' => 'Santa Ana de Yusguare',
            ],
            'Colón' => [
                'Trujillo' => 'Trujillo',
                'Balfate' => 'Balfate',
                'Iriona' => 'Iriona',
                'Limón' => 'Limón',
                'Sabá' => 'Sabá',
                'Santa Fe' => 'Santa Fe',
                'Santa Rosa de Aguán' => 'Santa Rosa de Aguán',
                'Sonaguera' => 'Sonaguera',
                'Tocoa' => 'Tocoa',
                'Bonito Oriental' => 'Bonito Oriental',
            ],
            'Comayagua' => [
                'Comayagua' => 'Comayagua',
                'Ajuterique' => 'Ajuterique',
                'El Rosario' => 'El Rosario',
                'Esquías' => 'Esquías',
                'Humuya' => 'Humuya',
                'La Libertad' => 'La Libertad',
                'Lamaní' => 'Lamaní',
                'La Trinidad' => 'La Trinidad',
                'Lejamaní' => 'Lejamaní',
                'Meámbar' => 'Meámbar',
                'Minas de Oro' => 'Minas de Oro',
                'Ojos de Agua' => 'Ojos de Agua',
                'San Jerónimo' => 'San Jerónimo',
                'San José de Comayagua' => 'San José de Comayagua',
                'San José del Potrero' => 'San José del Potrero',
                'San Luis' => 'San Luis',
                'San Sebastián' => 'San Sebastián',
                'Siguatepeque' => 'Siguatepeque',
                'Villa de San Antonio' => 'Villa de San Antonio',
                'Las Lajas' => 'Las Lajas',
                'Taulabé' => 'Taulabé',
            ],
            'Copán' => [
                'Santa Rosa de Copán' => 'Santa Rosa de Copán',
                'Cabañas' => 'Cabañas',
                'Concepción' => 'Concepción',
                'Copán Ruinas' => 'Copán Ruinas',
                'Corquín' => 'Corquín',
                'Cucuyagua' => 'Cucuyagua',
                'Dolores' => 'Dolores',
                'Dulce Nombre' => 'Dulce Nombre',
                'El Paraíso' => 'El Paraíso',
                'Florida' => 'Florida',
                'La Jigua' => 'La Jigua',
                'La Unión' => 'La Unión',
                'Nueva Arcadia' => 'Nueva Arcadia',
                'San Agustín' => 'San Agustín',
                'San Antonio' => 'San Antonio',
                'San Jerónimo' => 'San Jerónimo',
                'San José' => 'San José',
                'San Juan de Opoa' => 'San Juan de Opoa',
                'San Nicolás' => 'San Nicolás',
                'San Pedro' => 'San Pedro',
                'Santa Rita' => 'Santa Rita',
                'Trinidad de Copán' => 'Trinidad de Copán',
                'Veracruz' => 'Veracruz',
            ],
            'Cortés' => [
                'San Pedro Sula' => 'San Pedro Sula',
                'Choloma' => 'Choloma',
                'Omoa' => 'Omoa',
                'Pimienta' => 'Pimienta',
                'Potrerillos' => 'Potrerillos',
                'Puerto Cortés' => 'Puerto Cortés',
                'San Antonio de Cortés' => 'San Antonio de Cortés',
                'San Francisco de Yojoa' => 'San Francisco de Yojoa',
                'San Manuel' => 'San Manuel',
                'Santa Cruz de Yojoa' => 'Santa Cruz de Yojoa',
                'Villanueva' => 'Villanueva',
                'La Lima' => 'La Lima',
            ],
            'El Paraíso' => [
                'Yuscarán' => 'Yuscarán',
                'Alauca' => 'Alauca',
                'Danlí' => 'Danlí',
                'El Paraíso' => 'El Paraíso',
                'Güinope' => 'Güinope',
                'Jacaleapa' => 'Jacaleapa',
                'Liure' => 'Liure',
                'Morocelí' => 'Morocelí',
                'Oropolí' => 'Oropolí',
                'Potrerillos' => 'Potrerillos',
                'San Antonio de Flores' => 'San Antonio de Flores',
                'San Lucas' => 'San Lucas',
                'San Matías' => 'San Matías',
                'Soledad' => 'Soledad',
                'Teupasenti' => 'Teupasenti',
                'Texiguat' => 'Texiguat',
                'Vado Ancho' => 'Vado Ancho',
                'Yauyupe' => 'Yauyupe',
                'Trojes' => 'Trojes',
            ],
            'Francisco Morazán' => [
                'Distrito Central' => 'Distrito Central',
                'Alubarén' => 'Alubarén',
                'Cedros' => 'Cedros',
                'Curarén' => 'Curarén',
                'El Porvenir' => 'El Porvenir',
                'Guaimaca' => 'Guaimaca',
                'La Libertad' => 'La Libertad',
                'La Venta' => 'La Venta',
                'Lepaterique' => 'Lepaterique',
                'Maraita' => 'Maraita',
                'Marale' => 'Marale',
                'Nueva Armenia' => 'Nueva Armenia',
                'Ojojona' => 'Ojojona',
                'Orica' => 'Orica',
                'Reitoca' => 'Reitoca',
                'Sabanagrande' => 'Sabanagrande',
                'San Antonio de Oriente' => 'San Antonio de Oriente',
                'San Buenaventura' => 'San Buenaventura',
                'San Ignacio' => 'San Ignacio',
                'Cantarranas' => 'Cantarranas',
                'San Miguelito' => 'San Miguelito',
                'Santa Ana' => 'Santa Ana',
                'Santa Lucía' => 'Santa Lucía',
                'Talanga' => 'Talanga',
                'Tatumbla' => 'Tatumbla',
                'Valle de Ángeles' => 'Valle de Ángeles',
                'Villa de San Francisco' => 'Villa de San Francisco',
                'Vallecillo' => 'Vallecillo',
            ],
            'Gracias a Dios' => [
                'Puerto Lempira' => 'Puerto Lempira',
                'Brus Laguna' => 'Brus Laguna',
                'Ahuas' => 'Ahuas',
                'Juan Francisco Bulnes' => 'Juan Francisco Bulnes',
                'Villeda Morales' => 'Villeda Morales',
                'Wampusirpe' => 'Wampusirpe',
            ],
            'Intibucá' => [
                'La Esperanza' => 'La Esperanza',
                'Camasca' => 'Camasca',
                'Colomoncagua' => 'Colomoncagua',
                'Concepción' => 'Concepción',
                'Dolores' => 'Dolores',
                'Intibucá' => 'Intibucá',
                'Jesús de Otoro' => 'Jesús de Otoro',
                'Magdalena' => 'Magdalena',
                'Masaguara' => 'Masaguara',
                'San Antonio' => 'San Antonio',
                'San Isidro' => 'San Isidro',
                'San Juan' => 'San Juan',
                'San Marcos de la Sierra' => 'San Marcos de la Sierra',
                'San Miguel Guancapla' => 'San Miguel Guancapla',
                'Santa Lucía' => 'Santa Lucía',
                'Yamaranguila' => 'Yamaranguila',
                'San Francisco de Opalaca' => 'San Francisco de Opalaca',
            ],
            'Islas de la Bahía' => [
                'Roatán' => 'Roatán',
                'Guanaja' => 'Guanaja',
                'José Santos Guardiola' => 'José Santos Guardiola',
                'Utila' => 'Utila',
            ],
            'La Paz' => [
                'La Paz' => 'La Paz',
                'Aguanqueterique' => 'Aguanqueterique',
                'Cabañas' => 'Cabañas',
                'Cane' => 'Cane',
                'Chinacla' => 'Chinacla',
                'Guajiquiro' => 'Guajiquiro',
                'Lauterique' => 'Lauterique',
                'Marcala' => 'Marcala',
                'Mercedes de Oriente' => 'Mercedes de Oriente',
                'Opatoro' => 'Opatoro',
                'San Antonio del Norte' => 'San Antonio del Norte',
                'San José' => 'San José',
                'San Juan' => 'San Juan',
                'San Pedro de Tutule' => 'San Pedro de Tutule',
                'Santa Ana' => 'Santa Ana',
                'Santa Elena' => 'Santa Elena',
                'Santa María' => 'Santa María',
                'Santiago de Puringla' => 'Santiago de Puringla',
                'Yarula' => 'Yarula',
            ],
            'Lempira' => [
                'Gracias' => 'Gracias',
                'Belén' => 'Belén',
                'Candelaria' => 'Candelaria',
                'Cololaca' => 'Cololaca',
                'Erandique' => 'Erandique',
                'Gualcince' => 'Gualcince',
                'Guarita' => 'Guarita',
                'La Campa' => 'La Campa',
                'La Iguala' => 'La Iguala',
                'Las Flores' => 'Las Flores',
                'La Unión' => 'La Unión',
                'La Virtud' => 'La Virtud',
                'Lepaera' => 'Lepaera',
                'Mapulaca' => 'Mapulaca',
                'Piraera' => 'Piraera',
                'San Andrés' => 'San Andrés',
                'San Francisco' => 'San Francisco',
                'San Juan Guarita' => 'San Juan Guarita',
                'San Manuel Colohete' => 'San Manuel Colohete',
                'San Rafael' => 'San Rafael',
                'San Sebastián' => 'San Sebastián',
                'Santa Cruz' => 'Santa Cruz',
                'Talgua' => 'Talgua',
                'Tambla' => 'Tambla',
                'Tomalá' => 'Tomalá',
                'Valladolid' => 'Valladolid',
                'Virginia' => 'Virginia',
            ],
            'Ocotepeque' => [
                'Ocotepeque' => 'Ocotepeque',
                'Belén Gualcho' => 'Belén Gualcho',
                'Concepción' => 'Concepción',
                'Dolores Merendón' => 'Dolores Merendón',
                'Fraternidad' => 'Fraternidad',
                'La Encarnación' => 'La Encarnación',
                'La Labor' => 'La Labor',
                'Lucerna' => 'Lucerna',
                'Mercedes' => 'Mercedes',
                'San Fernando' => 'San Fernando',
                'San Francisco del Valle' => 'San Francisco del Valle',
                'San Jorge' => 'San Jorge',
                'San Marcos' => 'San Marcos',
                'Santa Fe' => 'Santa Fe',
                'Sensenti' => 'Sensenti',
                'Sinuapa' => 'Sinuapa',
            ],
            'Olancho' => [
                'Juticalpa' => 'Juticalpa',
                'Campamento' => 'Campamento',
                'Catacamas' => 'Catacamas',
                'Concordia' => 'Concordia',
                'Dulce Nombre de Culmí' => 'Dulce Nombre de Culmí',
                'El Rosario' => 'El Rosario',
                'Esquipulas del Norte' => 'Esquipulas del Norte',
                'Gualaco' => 'Gualaco',
                'Guarizama' => 'Guarizama',
                'Guata' => 'Guata',
                'Guayape' => 'Guayape',
                'Jano' => 'Jano',
                'La Unión' => 'La Unión',
                'Mangulile' => 'Mangulile',
                'Manto' => 'Manto',
                'Salamá' => 'Salamá',
                'San Esteban' => 'San Esteban',
                'San Francisco de Becerra' => 'San Francisco de Becerra',
                'San Francisco de la Paz' => 'San Francisco de la Paz',
                'Santa María del Real' => 'Santa María del Real',
                'Silca' => 'Silca',
                'Yocón' => 'Yocón',
                'Patuca' => 'Patuca',
            ],
            'Santa Bárbara' => [
                'Santa Bárbara' => 'Santa Bárbara',
                'Arada' => 'Arada',
                'Atima' => 'Atima',
                'Azacualpa' => 'Azacualpa',
                'Ceguaca' => 'Ceguaca',
                'Concepción del Norte' => 'Concepción del Norte',
                'Concepción del Sur' => 'Concepción del Sur',
                'Chinda' => 'Chinda',
                'El Níspero' => 'El Níspero',
                'Gualala' => 'Gualala',
                'Ilama' => 'Ilama',
                'Las Vegas' => 'Las Vegas',
                'Macuelizo' => 'Macuelizo',
                'Naranjito' => 'Naranjito',
                'Nuevo Celilac' => 'Nuevo Celilac',
                'Nueva Frontera' => 'Nueva Frontera',
                'Petoa' => 'Petoa',
                'Protección' => 'Protección',
                'Quimistán' => 'Quimistán',
                'San Francisco de Ojuera' => 'San Francisco de Ojuera',
                'San José de las Colinas' => 'San José de las Colinas',
                'San Luis' => 'San Luis',
                'San Marcos' => 'San Marcos',
                'San Nicolás' => 'San Nicolás',
                'San Pedro Zacapa' => 'San Pedro Zacapa',
                'San Vicente Centenario' => 'San Vicente Centenario',
                'Santa Rita' => 'Santa Rita',
                'Trinidad' => 'Trinidad',
            ],
            'Valle' => [
                'Nacaome' => 'Nacaome',
                'Alianza' => 'Alianza',
                'Amapala' => 'Amapala',
                'Aramecina' => 'Aramecina',
                'Caridad' => 'Caridad',
                'Goascorán' => 'Goascorán',
                'Langue' => 'Langue',
                'San Francisco de Coray' => 'San Francisco de Coray',
                'San Lorenzo' => 'San Lorenzo',
            ],
            'Yoro' => [
                'Yoro' => 'Yoro',
                'Arenal' => 'Arenal',
                'El Negrito' => 'El Negrito',
                'El Progreso' => 'El Progreso',
                'Jocón' => 'Jocón',
                'Morazán' => 'Morazán',
                'Olanchito' => 'Olanchito',
                'Santa Rita' => 'Santa Rita',
                'Sulaco' => 'Sulaco',
                'Victoria' => 'Victoria',
                'Yorito' => 'Yorito',
            ],
        ];
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
