<?php

namespace App\Filament\Owner\Resources\StockTransfers\Pages;

use App\Filament\Owner\Resources\StockTransfers\StockTransferResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStockTransfer extends EditRecord
{
    protected static string $resource = StockTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
