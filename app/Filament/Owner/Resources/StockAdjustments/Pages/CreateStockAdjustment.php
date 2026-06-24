<?php

namespace App\Filament\Owner\Resources\StockAdjustments\Pages;

use App\Filament\Owner\Resources\StockAdjustments\StockAdjustmentResource;
use App\Models\StockMovement;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateStockAdjustment extends CreateRecord
{
    protected static string $resource = StockAdjustmentResource::class;

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

        DB::transaction(function () use ($record) {
            foreach ($record->items as $item) {
                $diff = (float) $item->difference;

                if ($diff === 0.0) {
                    continue;
                }

                StockMovement::create([
                    'store_id' => $record->store_id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'type' => $diff > 0 ? 'in' : 'out',
                    'qty' => abs($diff),
                    'unit_price' => $item->unit_price ?? 0,
                    'reference_type' => 'stock_adjustment',
                    'reference_id' => $record->id,
                    'description' => 'Penyesuaian stok: '.$record->adjustment_number,
                    'created_by' => auth()->id(),
                ]);
            }
        });
    }
}
