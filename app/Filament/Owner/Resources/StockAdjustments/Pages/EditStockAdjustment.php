<?php

namespace App\Filament\Owner\Resources\StockAdjustments\Pages;

use App\Filament\Owner\Resources\StockAdjustments\StockAdjustmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStockAdjustment extends EditRecord
{
    protected static string $resource = StockAdjustmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
