<?php

namespace App\Policies;

use App\Models\User;

class ClientVisitDayPolicy extends BasePolicy
{
    /**
     * Nombre del modelo para generar los permisos
     */
    protected string $modelName = 'ClientVisitDay';
}