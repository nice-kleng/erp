<?php

namespace App\Filament\Owner\Resources\PurchaseOrders\Pages;

use App\Filament\Owner\Resources\PurchaseOrders\PurchaseOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePurchaseOrder extends CreateRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
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
