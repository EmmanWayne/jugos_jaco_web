<?php

namespace App\Filament\Resources\AssignedProductResource\Pages;

use App\Filament\Resources\AssignedProductResource;
use App\Models\AssignedProduct;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class CreateAssignedProduct extends CreateRecord
{
    protected static string $resource = AssignedProductResource::class;
    
    protected function handleRecordCreation(array $data): Model
    {
        $existingAssignment = AssignedProduct::where('employee_id', $data['employee_id'])
            ->where('date', $data['date'])
            ->first();
        
        if ($existingAssignment) {
            Notification::make()
                ->title('Error')
                ->body('Ya existe una asignación para este empleado en la fecha seleccionada.')
                ->danger()
                ->send();
            
            $this->halt();
        }
        
        return static::getModel()::create($data);
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
