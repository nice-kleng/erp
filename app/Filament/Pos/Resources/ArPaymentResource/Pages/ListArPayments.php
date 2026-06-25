<?php

namespace App\Filament\Pos\Resources\ArPaymentResource\Pages;

use App\Filament\Pos\Resources\ArPaymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListArPayments extends ListRecords
{
    protected static string $resource = ArPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Terima Pembayaran'),
        ];
    }
}
