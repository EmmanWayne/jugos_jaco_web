<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use App\Models\User;
use App\Enums\UserRole;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();
        
        $user = Auth::user();
        
        // Si el usuario es cajero y no tiene roles de admin/superadmin, filtrar por sus clientes asignados
        if ($user && $user->hasRole(UserRole::CASHEER->value) && 
            !$user->hasAnyRole([UserRole::ADMIN->value, UserRole::SUPERADMIN->value])) {
            $query->where('employee_id', $user->employee_id);
        }
        
        return $query;
    }
}
