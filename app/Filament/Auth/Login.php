<?php

namespace App\Filament\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getLoginFormComponent(), 
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }

    protected function getLoginFormComponent(): Component 
    {
        return TextInput::make('login')
            ->label('Identidad')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }
    
    protected function getCredentialsFromFormData(array $data): array
    { 
        // Buscar el empleado por su identidad
        $employee = \App\Models\Employee::where('identity', $data['login'])->first();
        
        if (!$employee) {
            $this->throwFailureValidationException();
        }
        
        if (!$employee->user) {
            $this->throwFailureValidationException();
        }
        
        // Devolver las credenciales usando el email del usuario encontrado
        return [
            'email' => $employee->user->email,
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}
