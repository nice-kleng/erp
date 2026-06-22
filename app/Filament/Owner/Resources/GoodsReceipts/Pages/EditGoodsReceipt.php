<?php

namespace App\Filament\Owner\Resources\GoodsReceipts\Pages;

use App\Filament\Owner\Resources\GoodsReceipts\GoodsReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGoodsReceipt extends EditRecord
{
    protected static string $resource = GoodsReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
