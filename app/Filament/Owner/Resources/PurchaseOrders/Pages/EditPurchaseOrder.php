<?php

namespace App\Filament\Owner\Resources\PurchaseOrders\Pages;

use App\Filament\Owner\Resources\PurchaseOrders\PurchaseOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPurchaseOrder extends EditRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $subtotal = $this->record->items->sum('subtotal');
        $discount = (float) ($this->data['discount'] ?? 0);
        $tax = (float) ($this->data['tax'] ?? 0);

        $this->record->update([
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'total' => $subtotal - $discount + $tax,
        ]);
    }
}
