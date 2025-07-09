<?php

namespace App\Filament\Resources;

use App\Enums\UnitCategoryEnum;
use App\Filament\Resources\UnitResource\Pages;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';

    protected static ?string $navigationGroup = 'Productos';

    protected static ?string $modelLabel = 'Unidad';

    protected static ?string $pluralModelLabel = 'Unidades';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Ej: Kilogramo, Litro, Caja'),

                        Forms\Components\TextInput::make('abbreviation')
                            ->label('Abreviatura')
                            ->required()
                            ->maxLength(10)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Ej: KG, LT, CAJ'),

                        Forms\Components\Select::make('category')
                            ->label('Categoría')
                            ->required()
                            ->options(UnitCategoryEnum::getOptions())
                            ->native(false)
                            ->searchable(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detalles Adicionales')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->maxLength(500)
                            ->rows(3)
                            ->placeholder('Descripción opcional de la unidad de medida'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true)
                            ->helperText('Define si la unidad está disponible para uso'),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('abbreviation')
                    ->label('Abreviatura')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('category_name')
                    ->label('Categoría')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Conteo' => 'primary',
                        'Peso' => 'success',
                        'Volumen' => 'info',
                        'Longitud' => 'warning',
                        'Área' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categoría')
                    ->options(UnitCategoryEnum::getOptions())
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->boolean()
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('name')
            ->striped();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información General')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nombre')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->weight('bold')
                            ->icon('heroicon-o-scale'),

                        Infolists\Components\TextEntry::make('abbreviation')
                            ->label('Abreviatura')
                            ->badge()
                            ->color('primary')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large),

                        Infolists\Components\TextEntry::make('category_name')
                            ->label('Categoría')
                            ->badge()
                            ->color(fn($record): string => match ($record->category) {
                                UnitCategoryEnum::COUNT => 'primary',
                                UnitCategoryEnum::WEIGHT => 'success',
                                UnitCategoryEnum::VOLUME => 'info',
                                UnitCategoryEnum::LENGTH => 'warning',
                                UnitCategoryEnum::AREA => 'danger',
                                default => 'gray',
                            })
                            ->icon(fn($record): string => match ($record->category) {
                                UnitCategoryEnum::COUNT => 'heroicon-o-hashtag',
                                UnitCategoryEnum::WEIGHT => 'heroicon-o-scale',
                                UnitCategoryEnum::VOLUME => 'heroicon-o-beaker',
                                UnitCategoryEnum::LENGTH => 'heroicon-o-arrow-long-right',
                                UnitCategoryEnum::AREA => 'heroicon-o-square-2-stack',
                                default => 'heroicon-o-question-mark-circle',
                            }),

                        Infolists\Components\IconEntry::make('is_active')
                            ->label('Estado')
                            ->getStateUsing(fn($record) => $record->is_active)
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger')
                            ->size(Infolists\Components\IconEntry\IconEntrySize::Large),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Descripción')
                    ->schema([
                        Infolists\Components\TextEntry::make('description')
                            ->label('')
                            ->placeholder('Sin descripción disponible')
                            ->helperText('Descripción detallada de la unidad de medida'),
                    ])
                    ->visible(fn($record) => !empty($record->description)),

                Infolists\Components\Section::make('Estadísticas de Uso')
                    ->schema([
                        Infolists\Components\TextEntry::make('product_units_count')
                            ->label('Productos que usan esta unidad')
                            ->getStateUsing(fn($record) => $record->productUnits()->count())
                            ->badge()
                            ->color('info')
                            ->icon('heroicon-o-cube'),
                    ])
                    ->visible(fn($record) => $record->productUnits()->count() > 0),

                Infolists\Components\Section::make('Información de Auditoría')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Fecha de Creación')
                            ->dateTime('d/m/Y h:i:s a')
                            ->icon('heroicon-o-calendar-days'),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Última Actualización')
                            ->dateTime('d/m/Y h:i:s a')
                            ->icon('heroicon-o-clock')
                            ->since(),
                    ])
                    ->columns(2)
                    ->collapsible(),
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
            'index' => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'view' => Pages\ViewUnit::route('/{record}'),
            'edit' => Pages\EditUnit::route('/{record}/edit'),
        ];
    }
}
