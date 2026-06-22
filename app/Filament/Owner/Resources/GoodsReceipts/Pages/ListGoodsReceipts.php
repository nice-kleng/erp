<?php

namespace App\Filament\Owner\Resources\GoodsReceipts\Pages;

use App\Filament\Owner\Resources\GoodsReceipts\GoodsReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGoodsReceipts extends ListRecords
{
    protected static string $resource = GoodsReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
