<?php

namespace App\Filament\Owner\Resources\StockMovements\Pages;

use App\Filament\Owner\Resources\StockMovements\StockMovementResource;
use Filament\Resources\Pages\ListRecords;

class ListStockMovements extends ListRecords
{
    protected static string $resource = StockMovementResource::class;
}
