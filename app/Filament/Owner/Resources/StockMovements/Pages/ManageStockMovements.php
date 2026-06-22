<?php

namespace App\Filament\Owner\Resources\StockMovements\Pages;

use App\Filament\Owner\Resources\StockMovements\StockMovementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageStockMovements extends ManageRecords
{
    protected static string $resource = StockMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
