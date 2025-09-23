<?php

namespace App\Policies;

use App\Models\User;

class SaleTaxTotalPolicy extends BasePolicy
{
    /**
     * Nombre del modelo para generar los permisos
     */
    protected string $modelName = 'SaleTaxTotal';
}