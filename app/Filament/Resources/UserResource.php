<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-s-user';

    protected static ?string $navigationGroup = 'Administración';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del usuario')
                    ->description('Información básica del usuario')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre de usuario')
                            ->placeholder('Nombre de usuario')
                            ->required()
                            ->maxLength(32),
                        Forms\Components\TextInput::make('email')
                            ->label('Correo electrónico')
                            ->placeholder('example@examplo.com')
                            ->email()
                            ->required()
                            ->maxLength(64),
                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->revealable(true)
                            ->required(fn($livewire) => $livewire instanceof Pages\CreateUser)
                            ->dehydrated(fn($state) => filled($state))
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->rules(['min:8'])
                            ->autocomplete('new-password')
                            ->helperText('La contraseña debe tener al menos 8 caracteres e incluir mayúsculas, minúsculas, números y símbolos')
                            ->live(),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Confirmar contraseña')
                            ->password()
                            ->revealable(true)
                            ->required(fn($livewire) => $livewire instanceof Pages\CreateUser)
                            ->dehydrated(false)
                            ->same('password')
                            ->validationAttribute('confirmación de contraseña'),
                        Forms\Components\Select::make('employee_id')
                            ->label('Empleado')
                            ->placeholder('Seleccionar empleado')
                            ->relationship('employee')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->full_name)
                            ->searchable()
                            ->preload()
                            ->validationAttribute('employee_id')
                            ->unique(ignoreRecord: true)
                            ->required(),
                        Forms\Components\Select::make('roles')
                            ->label('Roles')
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable(),
                        Forms\Components\Toggle::make('status')
                            ->label('Estado')
                            ->default(true)
                            ->required()
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre usuario')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo electrónico')
                    ->searchable(),
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Empleado')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('roles.name')
                    ->label('Rol')
                    ->color('primary')
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-s-check-circle')
                    ->falseIcon('heroicon-s-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable()
                    ->tooltip(fn(bool $state): string => $state ? 'Activo' : 'Inactivo', ['placement' => 'top'])
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Estado')
                    ->placeholder('Todos los estados')
                    ->trueLabel('Activo')
                    ->falseLabel('Inactivo')
                    ->nullable(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return 'Usuario';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Usuarios';
    }

    public static function getNavigationLabel(): string
    {
        return 'Usuarios';
    }

    public static function getNavigationSort(): int
    {
        return 4;
    }
}
