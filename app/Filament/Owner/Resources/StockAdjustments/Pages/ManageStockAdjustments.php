<?php

namespace App\Filament\Owner\Resources\StockAdjustments\Pages;

use App\Filament\Owner\Resources\StockAdjustments\StockAdjustmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageStockAdjustments extends ManageRecords
{
    protected static string $resource = StockAdjustmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
