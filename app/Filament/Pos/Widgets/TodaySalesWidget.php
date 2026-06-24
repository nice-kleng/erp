<?php

namespace App\Filament\Pos\Widgets;

use App\Models\Sale;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TodaySalesWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $storeId = Filament::getTenant()->id;
        $today = now()->startOfDay();

        $todaySales = Sale::where('store_id', $storeId)
            ->where('created_at', '>=', $today)
            ->where('status', 'completed');

        $total = (float) $todaySales->sum('total');
        $count = (int) $todaySales->count();
        $lastMonth = (float) Sale::where('store_id', $storeId)
            ->where('created_at', '>=', now()->subMonth())
            ->where('status', 'completed')
            ->sum('total');

        $change = $lastMonth > 0
            ? round((($total / ($lastMonth / 30)) - 1) * 100, 1)
            : 0;

        return [
            Stat::make('Penjualan Hari Ini', 'Rp '.number_format($total, 0, ',', '.'))
                ->description("{$count} transaksi")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Transaksi', "{$count}")
                ->description(($change >= 0 ? '+' : '')."{$change}% vs rata-rata bulanan")
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color($change >= 0 ? 'success' : 'danger'),
        ];
    }
}
