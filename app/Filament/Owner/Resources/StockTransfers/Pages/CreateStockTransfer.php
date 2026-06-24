<?php

namespace App\Filament\Owner\Resources\StockTransfers\Pages;

use App\Filament\Owner\Resources\StockTransfers\StockTransferResource;
use App\Models\StockMovement;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateStockTransfer extends CreateRecord
{
    protected static string $resource = StockTransferResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['store_id'] = Filament::getTenant()->id;
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        $record->refresh();

        if ($record->status !== 'received') {
            return;
        }

        $this->createStockMovements($record);
    }

    private function createStockMovements($record): void
    {
        foreach ($record->items as $item) {
            if ($record->from_store_id) {
                StockMovement::create([
                    'store_id' => $record->from_store_id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'type' => 'out',
                    'qty' => $item->qty,
                    'unit_price' => 0,
                    'reference_type' => 'stock_transfer',
                    'reference_id' => $record->id,
                    'description' => 'Transfer ke toko: '.($record->toStore?->name ?? 'N/A'),
                    'created_by' => auth()->id(),
                ]);
            }

            if ($record->to_store_id) {
                StockMovement::create([
                    'store_id' => $record->to_store_id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'type' => 'in',
                    'qty' => $item->qty,
                    'unit_price' => 0,
                    'reference_type' => 'stock_transfer',
                    'reference_id' => $record->id,
                    'description' => 'Transfer dari toko: '.($record->fromStore?->name ?? 'N/A'),
                    'created_by' => auth()->id(),
                ]);
            }
        }
    }
}
