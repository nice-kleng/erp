<?php

namespace App\Filament\Owner\Resources\ApPayments\Pages;

use App\Filament\Owner\Resources\ApPayments\ApPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApPayment extends EditRecord
{
    protected static string $resource = ApPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
