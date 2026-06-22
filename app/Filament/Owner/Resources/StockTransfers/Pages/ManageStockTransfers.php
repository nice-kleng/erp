<?php

namespace App\Filament\Owner\Resources\StockTransfers\Pages;

use App\Filament\Owner\Resources\StockTransfers\StockTransferResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageStockTransfers extends ManageRecords
{
    protected static string $resource = StockTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
