<?php

namespace App\Filament\Pos\Widgets;

use App\Models\Sale;
use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PosLatestSalesWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Sale::query()
                    ->where('store_id', Filament::getTenant()->id)
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('No. Invoice')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->default('Pelanggan Umum'),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Metode Bayar')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'Tunai',
                        'transfer' => 'Transfer',
                        'qris' => 'QRIS',
                        'debit' => 'Debit',
                        'credit_card' => 'Kartu Kredit',
                        default => ucfirst($state),
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'transfer' => 'info',
                        'qris' => 'warning',
                        'debit' => 'primary',
                        'credit_card' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
