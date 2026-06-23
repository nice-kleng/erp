<?php

namespace App\Filament\Owner\Resources\AccountPayables\Pages;

use App\Filament\Owner\Resources\AccountPayables\AccountPayableResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAccountPayable extends EditRecord
{
    protected static string $resource = AccountPayableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
