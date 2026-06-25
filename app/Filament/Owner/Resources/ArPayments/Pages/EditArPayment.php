<?php

namespace App\Filament\Owner\Resources\ArPayments\Pages;

use App\Filament\Owner\Resources\ArPayments\ArPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArPayment extends EditRecord
{
    protected static string $resource = ArPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
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
