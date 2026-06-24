<?php

namespace App\Filament\Owner\Resources\ApPayments\Pages;

use App\Filament\Owner\Resources\ApPayments\ApPaymentResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateApPayment extends CreateRecord
{
    protected static string $resource = ApPaymentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['store_id'] = Filament::getTenant()->id;
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->syncAccountPayable();
    }

    private function syncAccountPayable(): void
    {
        $ap = $this->record->accountPayable;
        if (! $ap) {
            return;
        }

        $totalPaid = (float) $ap->payments()->sum('amount');
        $totalAmount = (float) $ap->total_amount;

        $status = match (true) {
            $totalPaid >= $totalAmount => 'paid',
            $totalPaid > 0 => 'partial',
            default => 'unpaid',
        };

        $ap->update([
            'amount_paid' => $totalPaid,
            'status' => $status,
        ]);
    }
}
