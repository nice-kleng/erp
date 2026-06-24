<?php

namespace App\Filament\Owner\Resources\AccountPayables;

use App\Filament\Owner\Resources\AccountPayables\Pages\ListAccountPayables;
use App\Filament\Owner\Resources\AccountPayables\Pages\ViewAccountPayable;
use App\Models\AccountPayable;
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

class AccountPayableResource extends Resource
{
    protected static ?string $model = AccountPayable::class;

    protected static ?string $recordTitleAttribute = 'ap_number';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-credit-card';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Pembelian';
    }

    protected static ?int $navigationSort = 3;

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi AP')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('ap_number')
                            ->label('No. AP')
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'unpaid' => 'danger',
                                'partial' => 'warning',
                                'paid' => 'success',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'unpaid' => 'Belum Dibayar',
                                'partial' => 'Dibayar Sebagian',
                                'paid' => 'Lunas',
                                default => $state,
                            }),
                        TextEntry::make('supplier.name')
                            ->label('Supplier'),
                        TextEntry::make('due_date')
                            ->date()
                            ->color(fn (AccountPayable $record): string => $record->due_date->isPast() && $record->balance > 0 ? 'danger' : 'default'),
                    ]),

                Section::make('Detail Keuangan')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('total_amount')
                            ->label('Total')
                            ->money('IDR'),
                        TextEntry::make('amount_paid')
                            ->label('Terbayar')
                            ->money('IDR'),
                        TextEntry::make('balance')
                            ->label('Sisa')
                            ->money('IDR')
                            ->color(fn (AccountPayable $record): string => $record->balance > 0 ? 'danger' : 'success'),
                    ]),

                Section::make('Dokumen Sumber')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('goodsReceipt.receipt_number')
                            ->label('No. GR'),
                        TextEntry::make('goodsReceipt.purchaseOrder.order_number')
                            ->label('No. PO'),
                        TextEntry::make('goodsReceipt.status')
                            ->label('Status GR')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'completed' => 'success',
                                'draft' => 'warning',
                                'cancelled' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('goodsReceipt.purchaseOrder.status')
                            ->label('Status PO')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'ordered' => 'info',
                                'partially_received' => 'warning',
                                'received' => 'success',
                                'cancelled' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('goodsReceipt.received_at')
                            ->label('Tgl Terima')
                            ->date(),
                        TextEntry::make('goodsReceipt.purchaseOrder.expected_at')
                            ->label('Tgl Diharapkan')
                            ->date(),
                    ]),

                Section::make('Riwayat Pembayaran')
                    ->schema([
                        RepeatableEntry::make('payments')
                            ->schema([
                                TextEntry::make('payment_date')
                                    ->label('Tgl')
                                    ->date(),
                                TextEntry::make('amount')
                                    ->label('Jumlah')
                                    ->money('IDR'),
                                TextEntry::make('payment_method')
                                    ->label('Metode'),
                                TextEntry::make('reference')
                                    ->label('Referensi')
                                    ->placeholder('No. Referensi transfer / ID transaksi QRIS / dll.'),
                                TextEntry::make('notes')
                                    ->label('Catatan'),
                            ])
                            ->columns(5),
                    ]),

                Section::make('Informasi Lain')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime(),
                        TextEntry::make('creator.name')
                            ->label('Oleh'),
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
                TextColumn::make('ap_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('supplier.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('goodsReceipt.receipt_number')
                    ->label('GR')
                    ->searchable(),
                TextColumn::make('total_amount')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('amount_paid')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('balance')
                    ->money('IDR')
                    ->sortable()
                    ->color(fn (AccountPayable $record): string => $record->balance > 0 ? 'danger' : 'success'),
                TextColumn::make('due_date')
                    ->date()
                    ->sortable()
                    ->color(fn (AccountPayable $record): string => $record->due_date->isPast() && $record->balance > 0 ? 'danger' : 'default'),
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
                        'partial' => 'Dibayar Sebagian',
                        'paid' => 'Lunas',
                        default => $state,
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'unpaid' => 'Belum Dibayar',
                        'partial' => 'Dibayar Sebagian',
                        'paid' => 'Lunas',
                    ]),
                SelectFilter::make('supplier_id')
                    ->relationship('supplier', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccountPayables::route('/'),
            'view' => ViewAccountPayable::route('/{record}/view'),
        ];
    }
}
