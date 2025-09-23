<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

abstract class BasePolicy
{
    use HandlesAuthorization;

    /**
     * Nombre del modelo para generar los permisos
     */
    protected string $modelName;

    /**
     * Determina si el usuario puede ver cualquier modelo.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo($this->modelName . '.list');
    }

    /**
     * Determina si el usuario puede ver el modelo.
     */
    public function view(User $user, $model): bool
    {
        return $user->hasPermissionTo($this->modelName . '.view');
    }

    /**
     * Determina si el usuario puede crear modelos.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo($this->modelName . '.create');
    }

    /**
     * Determina si el usuario puede actualizar el modelo.
     */
    public function update(User $user, $model): bool
    {
        return $user->hasPermissionTo($this->modelName . '.update');
    }

    /**
     * Determina si el usuario puede eliminar el modelo.
     */
    public function delete(User $user, $model): bool
    {
        return $user->hasPermissionTo($this->modelName . '.delete');
    }
}