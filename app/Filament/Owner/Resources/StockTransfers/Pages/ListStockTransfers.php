<?php

namespace App\Filament\Owner\Resources\StockTransfers\Pages;

use App\Filament\Owner\Resources\StockTransfers\StockTransferResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStockTransfers extends ListRecords
{
    protected static string $resource = StockTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
