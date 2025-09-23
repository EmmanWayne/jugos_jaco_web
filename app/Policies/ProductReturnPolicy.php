<?php

namespace App\Policies;

use App\Models\User;

class ProductReturnPolicy extends BasePolicy
{
    /**
     * Nombre del modelo para generar los permisos
     */
    protected string $modelName = 'ProductReturn';
}