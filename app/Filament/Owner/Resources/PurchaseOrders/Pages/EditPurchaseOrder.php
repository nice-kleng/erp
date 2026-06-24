<?php

namespace App\Filament\Owner\Resources\PurchaseOrders\Pages;

use App\Filament\Owner\Resources\PurchaseOrders\PurchaseOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPurchaseOrder extends EditRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    public function syncRingkasan(): void
    {
        $items = $this->data['items'] ?? [];
        $subtotal = collect($items)->sum(fn ($item) => (float) ($item['subtotal'] ?? 0));
        $this->data['subtotal'] = $subtotal;
        $this->data['total'] = $subtotal - (float) ($this->data['discount'] ?? 0) + (float) ($this->data['tax'] ?? 0);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $items = $data['items'] ?? [];
        $data['subtotal'] = collect($items)->sum('subtotal');
        $data['total'] = $data['subtotal'] - ($data['discount'] ?? 0) + ($data['tax'] ?? 0);

        return $data;
    }
}
