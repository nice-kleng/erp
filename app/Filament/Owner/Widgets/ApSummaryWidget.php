<?php

namespace App\Filament\Owner\Widgets;

use App\Models\AccountPayable;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ApSummaryWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $storeId = Filament::getTenant()?->id;

        $totalOutstanding = AccountPayable::where('store_id', $storeId)
            ->whereIn('status', ['unpaid', 'partial'])
            ->get()
            ->sum(fn ($ap) => $ap->balance);

        $overdue = AccountPayable::where('store_id', $storeId)
            ->whereIn('status', ['unpaid', 'partial'])
            ->where('due_date', '<', now())
            ->get()
            ->sum(fn ($ap) => $ap->balance);

        $dueSoon = AccountPayable::where('store_id', $storeId)
            ->whereIn('status', ['unpaid', 'partial'])
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(7))
            ->get()
            ->sum(fn ($ap) => $ap->balance);

        return [
            Stat::make('Total Hutang', 'Rp '.number_format($totalOutstanding, 0, ',', '.'))
                ->description('Outstanding')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('info'),
            Stat::make('Jatuh Tempo', 'Rp '.number_format($overdue, 0, ',', '.'))
                ->description('Sudah lewat jatuh tempo')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger'),
            Stat::make('Jatuh Tempo 7 Hari', 'Rp '.number_format($dueSoon, 0, ',', '.'))
                ->description('Akan jatuh tempo dalam 7 hari')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),
        ];
    }
}
