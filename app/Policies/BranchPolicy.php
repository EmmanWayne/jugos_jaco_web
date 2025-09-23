<?php

namespace App\Policies;

use App\Models\User;

class BranchPolicy extends BasePolicy
{
    /**
     * Nombre del modelo para generar los permisos
     */
    protected string $modelName = 'Branch';
}