<?php

namespace App\Policies;

use App\Models\User;

class ProductUnitPolicy extends BasePolicy
{
    /**
     * Nombre del modelo para generar los permisos
     */
    protected string $modelName = 'ProductUnit';
}