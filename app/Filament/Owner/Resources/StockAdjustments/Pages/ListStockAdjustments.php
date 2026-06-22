<?php

namespace App\Filament\Owner\Resources\StockAdjustments\Pages;

use App\Filament\Owner\Resources\StockAdjustments\StockAdjustmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStockAdjustments extends ListRecords
{
    protected static string $resource = StockAdjustmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
