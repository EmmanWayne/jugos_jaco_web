<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SystemSettingResource\Pages;
use App\Filament\Resources\SystemSettingResource\RelationManagers;
use App\Models\SystemSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SystemSettingResource extends Resource
{
    protected static ?string $model = SystemSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';

    protected static ?string $modelLabel = 'Configuración';

    protected static ?string $pluralModelLabel = 'Configuración';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label('Nombre de la Empresa')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('logo_path')
                                    ->label('URL del Logo')
                                    ->required()
                                    ->url()
                                    ->live(onBlur: true)
                                    ->suffixIcon('heroicon-m-photo')
                                    ->helperText('Ingresa la URL completa de la imagen (ejemplo: https://ejemplo.com/imagen.jpg)')
                                    ->columnSpan(2),

                                Forms\Components\Placeholder::make('logo_preview')
                                    ->label('')
                                    ->content(function ($get) {
                                        $url = $get('logo_path');
                                        if (!$url) return 'No hay logo';
                                        return view('components.logo-preview', ['url' => $url]);
                                    })
                                    ->columnSpan(1),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),

                        Forms\Components\ColorPicker::make('theme_color')
                            ->label('Color del Tema')
                            ->required()
                            ->default('#001C4D')
                            ->hexColor()
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Activar Configuración')
                            ->helperText('Solo una configuración puede estar activa a la vez. Al activar esta, se desactivarán las demás.')
                            ->default(false)
                            ->required()
                            ->columnSpanFull()
                            ->extraAttributes(function ($state) {
                                return [
                                    'style' => $state ? 'background-color: #ff0000; padding: 10px; border-radius: 5px;' : 'background-color: #ffcccc; padding: 10px; border-radius: 5px;', // Change background color to red when active
                                ];
                            }),
                    ])
                    ->columns(1)
                    ->maxWidth('full') // Change maxWidth to full
                    ->extraAttributes([
                        'class' => 'mx-auto p-4', // Add padding for better spacing
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Nombre de la Empresa')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('logo_path')
                    ->label('Logo')
                    ->height(70)
                    ->width(100)
                    ->extraImgAttributes(['style' => 'object-fit: contain;']),
                Tables\Columns\ColorColumn::make('theme_color')
                    ->label('Color del Tema'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('is_active', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
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
            'index' => Pages\ListSystemSettings::route('/'),
            'create' => Pages\CreateSystemSetting::route('/create'),
            'edit' => Pages\EditSystemSetting::route('/{record}/edit'),
        ];
    }
}
