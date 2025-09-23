<?php

namespace App\Policies;

use App\Models\User;

class ResourceMediaPolicy extends BasePolicy
{
    /**
     * Nombre del modelo para generar los permisos
     */
    protected string $modelName = 'ResourceMedia';
}