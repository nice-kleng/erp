<?php

namespace App\Filament\Owner\Resources\Productions\Pages;

use App\Filament\Owner\Resources\Productions\ProductionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductions extends ListRecords
{
    protected static string $resource = ProductionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
