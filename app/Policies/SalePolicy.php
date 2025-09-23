<?php

namespace App\Policies;

use App\Models\User;

class SalePolicy extends BasePolicy
{
    /**
     * Nombre del modelo para generar los permisos
     */
    protected string $modelName = 'Sale';
}