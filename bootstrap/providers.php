<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\OwnerPanelProvider;
use App\Providers\Filament\PosPanelProvider;
use App\Providers\Filament\SuperadminPanelProvider;

return [
    AppServiceProvider::class,
    SuperadminPanelProvider::class,
    OwnerPanelProvider::class,
    PosPanelProvider::class,
];
