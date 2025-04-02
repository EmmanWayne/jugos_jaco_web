<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Category;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Productos';

    public static function form(Form $form): Form
    {
        return $form

            ->schema([
                Section::make('Información del producto')  // Título de la sección
                    ->description('En esta sección se registra la información de los productos.') // Descripción
                    ->schema([
                        Section::make('')
                            ->columns(3)
                            ->schema([
                                Forms\Components\TextInput::make('code')
                                    ->label('Código')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(20)
                                    ->afterStateUpdated(fn($state, callable $set) => self::validateCode($state)),
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre')
                                    ->required()
                                    ->maxLength(50),
                                Select::make('category_id')
                                    ->label('Categoría')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label('Nombre')
                                            ->required(),
                                    ])
                                    ->required(),
                            ]),

                        Section::make('')
                            ->columns(1)
                            ->schema([
                                Forms\Components\Textarea::make('description')
                                    ->label('Descripción')
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                            ]),

                        Section::make('')
                            ->columns(3)
                            ->schema([
                                Select::make('content_type')
                                    ->label('Tipo de contenido')
                                    ->required()
                                    ->options([
                                        'ml' => 'Mililitros',
                                        'mg' => 'Miligramos',
                                        'u' => 'Unidad',
                                        'onz' => 'Onzas',
                                    ])
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\TextInput::make('content')
                                    ->label('Contenido')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxLength(16),
                                Forms\Components\TextInput::make('cost')
                                    ->label('Costo')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('L.'),
                            ]),


                    ]),

            ]);
    }

    /**
     * Valida la identidad y lanza una notificación si ya existe.
     */
    protected static function validateCode($code)
    {
        if (\App\Models\Product::where('code', $code)->exists()) {
            Notification::make()
                ->title('¡Atención!')
                ->body('El código ya está registrado en el sistema.')
                ->danger()
                ->send();
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoría')
                    ->sortable(),
                Tables\Columns\TextColumn::make('content')
                    ->label('Contenido')
                    ->formatStateUsing(fn($record) => "{$record->content} {$record->content_type}")
                    ->searchable(),
                Tables\Columns\TextColumn::make('cost')
                    ->label('Costo')
                    ->formatStateUsing(fn($record) => "L. {$record->cost}")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(30)
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Productos';
    }

    public static function getModelLabel(): string
    {
        return 'Producto';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Productos';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-cube';
    }

    public static function getNavigationSort(): int
    {
        return 1;
    }
}
