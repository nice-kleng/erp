<?php

namespace App\Filament\Owner\Resources\PurchaseOrders\Pages;

use App\Filament\Owner\Resources\PurchaseOrders\PurchaseOrderResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreatePurchaseOrder extends CreateRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    public function syncRingkasan(): void
    {
        $items = $this->data['items'] ?? [];
        $subtotal = collect($items)->sum(fn ($item) => (float) ($item['subtotal'] ?? 0));
        $this->data['subtotal'] = $subtotal;
        $this->data['total'] = $subtotal - (float) ($this->data['discount'] ?? 0) + (float) ($this->data['tax'] ?? 0);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['store_id'] = Filament::getTenant()->id;
        $data['created_by'] = auth()->id();

        $items = $data['items'] ?? [];
        $data['subtotal'] = collect($items)->sum('subtotal');
        $data['total'] = $data['subtotal'] - ($data['discount'] ?? 0) + ($data['tax'] ?? 0);

        return $data;
    }
}
