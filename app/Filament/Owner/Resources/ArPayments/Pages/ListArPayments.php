<?php

namespace App\Filament\Owner\Resources\ArPayments\Pages;

use App\Filament\Owner\Resources\ArPayments\ArPaymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListArPayments extends ListRecords
{
    protected static string $resource = ArPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
