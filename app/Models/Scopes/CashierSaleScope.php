<?php

namespace App\Models\Scopes;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class CashierSaleScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Solo aplicar el filtro si hay un usuario autenticado
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();
        
        // Solo aplicar el filtro si el usuario tiene el rol de cajero
        // y no tiene roles de mayor jerarquía (admin o superadmin)
        if ($user->hasRole(UserRole::CASHEER->value) && 
            !$user->hasAnyRole([UserRole::ADMIN->value, UserRole::SUPERADMIN->value])) {
            
            // Filtrar solo las ventas creadas por el cajero actual
            $builder->where('created_by', $user->id);
        }
    }
}