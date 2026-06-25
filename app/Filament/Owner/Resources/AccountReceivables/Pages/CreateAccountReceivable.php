<?php

namespace App\Filament\Owner\Resources\AccountReceivables\Pages;

use App\Filament\Owner\Resources\AccountReceivables\AccountReceivableResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateAccountReceivable extends CreateRecord
{
    protected static string $resource = AccountReceivableResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['store_id'] = Filament::getTenant()->id;
        $data['created_by'] = auth()->id();

        return $data;
    }
}
