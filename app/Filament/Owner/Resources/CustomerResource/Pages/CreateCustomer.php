<?php

namespace App\Filament\Owner\Resources\CustomerResource\Pages;

use App\Filament\Owner\Resources\CustomerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;
}
