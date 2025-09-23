<?php

namespace App\Policies;

use App\Models\User;

class LocationPolicy extends BasePolicy
{
    /**
     * Nombre del modelo para generar los permisos
     */
    protected string $modelName = 'Location';
}