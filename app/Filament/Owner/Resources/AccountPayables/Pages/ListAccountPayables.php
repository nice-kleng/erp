<?php

namespace App\Filament\Owner\Resources\AccountPayables\Pages;

use App\Filament\Owner\Resources\AccountPayables\AccountPayableResource;
use Filament\Resources\Pages\ListRecords;

class ListAccountPayables extends ListRecords
{
    protected static string $resource = AccountPayableResource::class;
}
