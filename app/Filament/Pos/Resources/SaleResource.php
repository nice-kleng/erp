<?php

namespace App\Filament\Pos\Resources;

use App\Filament\Pos\Resources\SaleResource\Pages\ListSales;
use App\Filament\Pos\Resources\SaleResource\Pages\ViewSale;
use App\Models\Sale;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $recordTitleAttribute = 'invoice_number';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-receipt-percent';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Transaksi';
    }

    protected static ?int $navigationSort = 2;

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Penjualan')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('invoice_number')
                            ->label('No. Invoice')
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'completed' => 'success',
                                default => 'gray',
                            }),
                        TextEntry::make('customer.name')
                            ->label('Pelanggan'),
                        TextEntry::make('cashier.name')
                            ->label('Kasir'),
                        TextEntry::make('created_at')
                            ->label('Waktu')
                            ->dateTime(),
                        TextEntry::make('payment_method')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'cash' => 'Tunai',
                                'transfer' => 'Transfer',
                                'qris' => 'QRIS',
                                'debit' => 'Debit',
                                'kredit' => 'Kredit',
                                default => $state,
                            }),
                    ]),

                Section::make('Item Penjualan')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->schema([
                                TextEntry::make('product.name')
                                    ->label('Produk'),
                                TextEntry::make('productVariant.name')
                                    ->label('Varian'),
                                TextEntry::make('qty')
                                    ->label('Qty'),
                                TextEntry::make('unit_price')
                                    ->label('Harga')
                                    ->money('IDR'),
                                TextEntry::make('discount')
                                    ->label('Diskon')
                                    ->money('IDR'),
                                TextEntry::make('subtotal')
                                    ->label('Subtotal')
                                    ->money('IDR'),
                            ])
                            ->columns(4),
                    ]),

                Section::make('Ringkasan Keuangan')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('subtotal')
                            ->money('IDR'),
                        TextEntry::make('discount')
                            ->money('IDR'),
                        TextEntry::make('total')
                            ->money('IDR')
                            ->weight(FontWeight::Bold)
                            ->color('primary'),
                    ]),

                Section::make('Pembayaran')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('payment_method')
                            ->label('Metode')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'cash' => 'Tunai',
                                'transfer' => 'Transfer',
                                'qris' => 'QRIS',
                                'debit' => 'Debit',
                                'kredit' => 'Kredit',
                                default => $state,
                            }),
                        TextEntry::make('amount_paid')
                            ->label('Dibayar')
                            ->money('IDR'),
                        TextEntry::make('change')
                            ->label('Kembalian')
                            ->money('IDR'),
                        TextEntry::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull()
                            ->visible(fn (?string $state): bool => filled($state)),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->searchable(),
                TextColumn::make('cashier.name')
                    ->label('Kasir')
                    ->searchable(),
                TextColumn::make('total')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'Tunai',
                        'transfer' => 'Transfer',
                        'qris' => 'QRIS',
                        'debit' => 'Debit',
                        'kredit' => 'Kredit',
                        default => $state,
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Waktu'),
            ])
            ->filters([
                SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Tunai',
                        'transfer' => 'Transfer',
                        'qris' => 'QRIS',
                        'debit' => 'Debit',
                        'kredit' => 'Kredit',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSales::route('/'),
            'view' => ViewSale::route('/{record}/view'),
        ];
    }
}
