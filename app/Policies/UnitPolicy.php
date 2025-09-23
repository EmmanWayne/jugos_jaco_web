<?php

namespace App\Policies;

use App\Models\User;

class UnitPolicy extends BasePolicy
{
    /**
     * Nombre del modelo para generar los permisos
     */
    protected string $modelName = 'Unit';
}