<?php

namespace App\Filament\Owner\Resources\Productions\Pages;

use App\Filament\Owner\Resources\Productions\ProductionResource;
use App\Models\StockMovement;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateProduction extends CreateRecord
{
    protected static string $resource = ProductionResource::class;

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
