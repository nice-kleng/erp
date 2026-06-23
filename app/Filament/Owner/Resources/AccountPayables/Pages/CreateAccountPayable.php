<?php

namespace App\Filament\Owner\Resources\AccountPayables\Pages;

use App\Filament\Owner\Resources\AccountPayables\AccountPayableResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAccountPayable extends CreateRecord
{
    protected static string $resource = AccountPayableResource::class;
}
