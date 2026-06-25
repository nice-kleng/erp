<?php

namespace App\Filament\Owner\Resources\AccountReceivables;

use App\Filament\Owner\Resources\AccountReceivables\Pages\CreateAccountReceivable;
use App\Filament\Owner\Resources\AccountReceivables\Pages\ListAccountReceivables;
use App\Filament\Owner\Resources\AccountReceivables\Pages\ViewAccountReceivable;
use App\Models\AccountReceivable;
use App\Models\Customer;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AccountReceivableResource extends Resource
{
    protected static ?string $model = AccountReceivable::class;

    protected static ?string $recordTitleAttribute = 'ar_number';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-receipt-percent';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Penjualan';
    }

    protected static ?int $navigationSort = 1;

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Piutang')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('ar_number')
                            ->label('No. Piutang')
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
                        TextEntry::make('customer.name')
                            ->label('Pelanggan'),
                        TextEntry::make('due_date')
                            ->date()
                            ->color(fn (AccountReceivable $record): string => $record->due_date->isPast() && $record->balance > 0 ? 'danger' : 'default'),
                        TextEntry::make('sale.invoice_number')
                            ->label('No. Invoice'),
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
                            ->color(fn (AccountReceivable $record): string => $record->balance > 0 ? 'danger' : 'success'),
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
                                    ->label('Referensi'),
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
                TextColumn::make('ar_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sale.invoice_number')
                    ->label('Invoice')
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
                    ->color(fn (AccountReceivable $record): string => $record->balance > 0 ? 'danger' : 'success'),
                TextColumn::make('due_date')
                    ->date()
                    ->sortable()
                    ->color(fn (AccountReceivable $record): string => $record->due_date->isPast() && $record->balance > 0 ? 'danger' : 'default'),
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
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'unpaid' => 'Belum Dibayar',
                        'partial' => 'Dibayar Sebagian',
                        'paid' => 'Lunas',
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
            'index' => ListAccountReceivables::route('/'),
            'create' => CreateAccountReceivable::route('/create'),
            'view' => ViewAccountReceivable::route('/{record}/view'),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                \Filament\Schemas\Components\Section::make('Informasi Piutang')
                    ->columns(2)
                    ->schema([
                        Select::make('customer_id')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Pelanggan')
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if (! $state) {
                                    return;
                                }
                                $customer = Customer::find($state);
                                if ($customer) {
                                    $set('due_date', now()->addDays($customer->ar_due_days ?? 7)->format('Y-m-d'));
                                }
                            }),
                        Select::make('sale_id')
                            ->relationship('sale', 'invoice_number', fn ($query) => $query->where('payment_method', 'credit'))
                            ->searchable()
                            ->preload()
                            ->label('Dari Penjualan (opsional)'),
                        TextInput::make('ar_number')
                            ->required()
                            ->maxLength(255)
                            ->default(fn () => 'AR-'.now()->format('Ymd').'-'.Str::upper(Str::random(4))),
                        DatePicker::make('due_date')
                            ->required()
                            ->default(now()->addDays(7)),
                        TextInput::make('total_amount')
                            ->label('Total Piutang')
                            ->required()
                            ->numeric()
                            ->prefix('Rp'),
                        Textarea::make('notes')
                            ->rows(3),
                    ]),
            ]);
    }
}
