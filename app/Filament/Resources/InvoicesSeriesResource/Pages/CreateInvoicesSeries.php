<?php

namespace App\Filament\Resources\InvoicesSeriesResource\Pages;

use App\Filament\Resources\InvoicesSeriesResource;
use App\Filament\Support\FilamentNotification;
use App\Models\InvoicesSeries;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoicesSeries extends CreateRecord
{
    protected static string $resource = InvoicesSeriesResource::class;
    
    protected function beforeCreate(): void
    {
        $this->validateActiveSeriesForBranch();
    }
    
    protected function validateActiveSeriesForBranch(): void
    {
        $branchId = $this->data['branch_id'];
        
        $existingActiveSeries = InvoicesSeries::where('branch_id', $branchId)
            ->where('status', 'Activa')
            ->exists();
        
        if ($existingActiveSeries) {
            FilamentNotification::error(
                'Error al crear la serie de facturación',
                'Ya existe una serie de facturación activa para esta sucursal.'
            );
            
            $this->halt();
        }
    }
}
