<?php

namespace App\Filament\Owner\Resources\GoodsReceipts\Pages;

use App\Filament\Owner\Resources\GoodsReceipts\GoodsReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditGoodsReceipt extends EditRecord
{
    protected static string $resource = GoodsReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->record;

        if ($record->status !== 'completed') {
            return;
        }

        if ($record->accountPayable()->exists()) {
            return;
        }

        $total = $record->items->sum(fn ($item) => $item->qty_received * $item->unit_price);

        $record->accountPayable()->create([
            'store_id' => $record->store_id,
            'supplier_id' => $record->purchaseOrder->supplier_id,
            'ap_number' => 'AP-'.now()->format('Ymd').'-'.Str::upper(Str::random(4)),
            'total_amount' => $total,
            'amount_paid' => 0,
            'due_date' => now()->addDays(30),
            'status' => 'unpaid',
            'created_by' => auth()->id(),
        ]);
    }
}
