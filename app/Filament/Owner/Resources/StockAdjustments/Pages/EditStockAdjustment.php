<?php

namespace App\Filament\Owner\Resources\StockAdjustments\Pages;

use App\Filament\Owner\Resources\StockAdjustments\StockAdjustmentResource;
use App\Models\StockMovement;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditStockAdjustment extends EditRecord
{
    protected static string $resource = StockAdjustmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->record;
        $record->refresh();

        StockMovement::where('reference_type', 'stock_adjustment')
            ->where('reference_id', $record->id)
            ->delete();

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
