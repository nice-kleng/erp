<?php

namespace App\Filament\Owner\Widgets;

use App\Models\AccountPayable;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class ApDueTable extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AccountPayable::where('store_id', Filament::getTenant()?->id)
                    ->with('supplier')
                    ->whereIn('status', ['unpaid', 'partial'])
                    ->latest()
            )
            ->columns([
                TextColumn::make('ap_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('supplier.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('amount_paid')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('balance')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('due_date')
                    ->date()
                    ->sortable()
                    ->color(fn (AccountPayable $record): string => match (true) {
                        $record->due_date < now() => 'danger',
                        $record->due_date <= now()->addDays(7) => 'warning',
                        default => 'success',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'unpaid' => 'danger',
                        'partial' => 'warning',
                        'paid' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'unpaid' => 'Belum Dibayar',
                        'partial' => 'Sebagian',
                        'paid' => 'Lunas',
                        default => $state,
                    }),
            ])
            ->defaultSort('due_date', 'asc')
            ->paginated(false);
    }
}
