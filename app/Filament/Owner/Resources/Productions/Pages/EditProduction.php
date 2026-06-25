<?php

namespace App\Filament\Owner\Resources\Productions\Pages;

use App\Filament\Owner\Resources\Productions\ProductionResource;
use App\Models\StockMovement;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditProduction extends EditRecord
{
    protected static string $resource = ProductionResource::class;

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

        StockMovement::where('reference_type', 'production')
            ->where('reference_id', $record->id)
            ->delete();

        if ($record->status !== 'completed') {
            return;
        }

        DB::transaction(function () use ($record) {
            $totalCost = 0;

            foreach ($record->ingredients as $ingredient) {
                $subtotal = (float) $ingredient->qty_used * (float) $ingredient->unit_price;
                $ingredient->update(['subtotal' => $subtotal]);

                StockMovement::create([
                    'store_id' => $record->store_id,
                    'product_id' => $ingredient->product_id,
                    'product_variant_id' => $ingredient->product_variant_id,
                    'type' => 'out',
                    'qty' => $ingredient->qty_used,
                    'unit_price' => $ingredient->unit_price,
                    'reference_type' => 'production',
                    'reference_id' => $record->id,
                    'description' => 'Produksi: '.$record->production_number.' ('.$record->recipe->name.')',
                    'created_by' => auth()->id(),
                ]);

                $totalCost += $subtotal;
            }

            StockMovement::create([
                'store_id' => $record->store_id,
                'product_id' => $record->product_id,
                'product_variant_id' => null,
                'type' => 'in',
                'qty' => $record->qty_produced,
                'unit_price' => $record->qty_produced > 0 ? round($totalCost / (float) $record->qty_produced) : 0,
                'reference_type' => 'production',
                'reference_id' => $record->id,
                'description' => 'Hasil produksi: '.$record->production_number,
                'created_by' => auth()->id(),
            ]);

            $record->update(['total_cost' => $totalCost]);
        });
    }
}
