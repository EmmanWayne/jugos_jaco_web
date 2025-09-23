<?php

namespace App\Policies;

use App\Models\User;

class InvoicesSeriesPolicy extends BasePolicy
{
    /**
     * Nombre del modelo para generar los permisos
     */
    protected string $modelName = 'InvoicesSeries';
}