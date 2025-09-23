<?php

namespace App\Policies;

use App\Models\User;

class TaxCategoryPolicy extends BasePolicy
{
    /**
     * Nombre del modelo para generar los permisos
     */
    protected string $modelName = 'TaxCategory';
}