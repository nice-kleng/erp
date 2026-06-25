<?php

namespace App\Filament\Owner\Resources\ArPayments\Pages;

use App\Filament\Owner\Resources\ArPayments\ArPaymentResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateArPayment extends CreateRecord
{
    protected static string $resource = ArPaymentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['store_id'] = Filament::getTenant()->id;
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->syncAccountReceivable();
    }

    private function syncAccountReceivable(): void
    {
        $ar = $this->record->accountReceivable;
        if (! $ar) {
            return;
        }

        $totalPaid = (float) $ar->payments()->sum('amount');
        $totalAmount = (float) $ar->total_amount;

        $status = match (true) {
            $totalPaid >= $totalAmount => 'paid',
            $totalPaid > 0 => 'partial',
            default => 'unpaid',
        };

        $ar->update([
            'amount_paid' => $totalPaid,
            'status' => $status,
        ]);
    }
}
