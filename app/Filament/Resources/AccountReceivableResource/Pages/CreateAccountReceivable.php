<?php

namespace App\Filament\Resources\AccountReceivableResource\Pages;

use App\Filament\Resources\AccountReceivableResource;
use App\Models\Sale;
use App\Services\AccountReceivableService;
use App\Services\ExceptionHandlerService;
use Carbon\Carbon;
use Exception;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAccountReceivable extends CreateRecord
{
    protected static string $resource = AccountReceivableResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        try {
            $service = app(AccountReceivableService::class);

            $sale = isset($data['sales_id']) && $data['sales_id']
                ? Sale::findOrFail($data['sales_id'])
                : null;

            // Convert due_date (string) to Carbon instance to match service signature
            $dueDate = isset($data['due_date']) && $data['due_date']
                ? Carbon::parse($data['due_date'])
                : null;

            return $service->create(
                sale: $sale,
                totalAmount: $sale ? null : ($data['total_amount'] ?? null),
                name: $data['name'] ?? null,
                notes: $data['notes'] ?? null,
                dueDate: $dueDate,
                amountPaidNow: $sale ? ($data['amount_paid_now'] ?? null) : null,
            );
        } catch (Exception $exc) {
            ExceptionHandlerService::handle($exc);
            $this->halt();

            throw $exc;
        }
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Cuenta por cobrar creada exitosamente';
    }
}
