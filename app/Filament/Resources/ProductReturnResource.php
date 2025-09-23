<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductReturnResource\Pages;
use App\Models\ProductReturn;
use App\Services\ProductReturnService;
use App\Enums\ProductReturnTypeEnum;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class ProductReturnResource extends Resource
{
    protected static ?string $model = ProductReturn::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-left';
    
    protected static ?string $navigationLabel = 'Devoluciones';
    
    protected static ?string $modelLabel = 'Devolución';
    
    protected static ?string $pluralModelLabel = 'Devoluciones';
    
    protected static ?string $navigationGroup = 'Inventario';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información de la Devolución')
                    ->description('Registre los detalles de la devolución de producto')
                    ->schema([
                        Section::make('')
                            ->columns(2)
                            ->schema([
                                Select::make('product_id')
                                    ->label('Producto')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                    
                                Select::make('employee_id')
                                    ->label('Empleado')
                                    ->relationship('employee', 'first_name')
                                    ->getOptionLabelFromRecordUsing(fn($record) => $record->full_name)
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                    
                                TextInput::make('quantity')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->step(0.01)
                                    ->minValue(0.01)
                                    ->required(),
                            ]),
                            
                        Section::make('')
                            ->columns(1)
                            ->schema([
                                Select::make('type')
                                    ->label('Tipo de Devolución')
                                    ->options(ProductReturnTypeEnum::getOptions())
                                    ->required()
                                    ->native(false),
                                    
                                TextInput::make('reason')
                                    ->label('Motivo')
                                    ->required()
                                    ->maxLength(255)
                                    ->minLength(3),
                                    
                                Textarea::make('description')
                                    ->label('Descripción')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                                    
                                Toggle::make('affects_inventory')
                                    ->label('Afecta Inventario')
                                    ->helperText('Determina si esta devolución debe generar movimientos de inventario')
                                    ->default(true)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                    
                TextColumn::make('product.name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('employee.full_name')
                    ->label('Empleado')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn($state) => $state->getColor())
                    ->formatStateUsing(fn($state) => $state->getLabel()),
                    
                TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->numeric(2)
                    ->sortable(),
                    
                TextColumn::make('reason')
                    ->label('Motivo')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),
                    
                TextColumn::make('affects_inventory')
                    ->label('Afecta Inventario')
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn($state) => $state ? 'Sí' : 'No')
                    ->sortable(),
                    

                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipo de Devolución')
                    ->options(ProductReturnTypeEnum::getOptions()),
                    
                SelectFilter::make('product_id')
                    ->label('Producto')
                    ->relationship('product', 'name')
                    ->searchable(),
                    
                SelectFilter::make('employee_id')
                    ->label('Empleado')
                    ->relationship('employee', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->full_name)
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Model $record) {
                        $productReturnService = new ProductReturnService();
                        $productReturnService->reverseInventoryMovement($record);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Collection $records) {
                            $productReturnService = new ProductReturnService();
                            foreach ($records as $record) {
                                $productReturnService->reverseInventoryMovement($record);
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductReturns::route('/'),
            'create' => Pages\CreateProductReturn::route('/create'),
            'view' => Pages\ViewProductReturn::route('/{record}'),
        ];
    }


}