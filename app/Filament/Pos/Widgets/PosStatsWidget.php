<?php

namespace App\Filament\Pos\Widgets;

use App\Models\Sale;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PosStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $tenantId = Filament::getTenant()->id;
        $today = Carbon::today();

        $todaySales = Sale::where('store_id', $tenantId)
            ->whereDate('created_at', $today)
            ->where('status', 'completed');

        $totalRevenue = (clone $todaySales)->sum('total');
        $totalOrders = (clone $todaySales)->count();

        $yesterdaySales = Sale::where('store_id', $tenantId)
            ->whereDate('created_at', $today->copy()->subDay())
            ->where('status', 'completed');

        $yesterdayRevenue = (clone $yesterdaySales)->sum('total');
        $yesterdayOrders = (clone $yesterdaySales)->count();

        // Calculate Revenue trend
        $revenueDiff = $totalRevenue - $yesterdayRevenue;
        $revenueIcon = $revenueDiff >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
        $revenueColor = $revenueDiff >= 0 ? 'success' : 'danger';
        $revenueDesc = $revenueDiff >= 0 ? 'Naik dari kemarin' : 'Turun dari kemarin';

        // Calculate Orders trend
        $ordersDiff = $totalOrders - $yesterdayOrders;
        $ordersIcon = $ordersDiff >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
        $ordersColor = $ordersDiff >= 0 ? 'success' : 'danger';
        $ordersDesc = $ordersDiff >= 0 ? 'Naik dari kemarin' : 'Turun dari kemarin';

        return [
            Stat::make('Pendapatan Hari Ini', 'Rp '.number_format($totalRevenue, 0, ',', '.'))
                ->description($revenueDesc)
                ->descriptionIcon($revenueIcon)
                ->color($revenueColor),

            Stat::make('Total Transaksi Hari Ini', $totalOrders)
                ->description($ordersDesc)
                ->descriptionIcon($ordersIcon)
                ->color($ordersColor),
        ];
    }
}
