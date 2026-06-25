<?php

namespace App\Filament\Owner\Resources\ArPayments;

use App\Filament\Owner\Resources\ArPayments\Pages\CreateArPayment;
use App\Filament\Owner\Resources\ArPayments\Pages\EditArPayment;
use App\Filament\Owner\Resources\ArPayments\Pages\ListArPayments;
use App\Models\AccountReceivable;
use App\Models\ArPayment;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ArPaymentResource extends Resource
{
    protected static ?string $model = ArPayment::class;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-banknotes';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Penjualan';
    }

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Pembayaran')
                    ->columns(2)
                    ->schema([
                        Select::make('account_receivable_id')
                            ->relationship('accountReceivable', 'ar_number', fn ($query) => $query->whereIn('status', ['unpaid', 'partial']))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Piutang')
                            ->live(true)
                            ->afterStateUpdated(function ($state, $set) {
                                if (! $state) {
                                    return;
                                }
                                $ar = AccountReceivable::find($state);
                                if ($ar) {
                                    $set('amount', $ar->balance);
                                }
                            })
                            ->getOptionLabelFromRecordUsing(fn (AccountReceivable $record): string => $record->ar_number.' — Rp '.number_format($record->balance, 0, ',', '.')),
                        TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->rules([
                                fn ($get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $arId = $get('account_receivable_id');
                                    if (! $arId) {
                                        return;
                                    }
                                    $ar = AccountReceivable::find($arId);
                                    if (! $ar) {
                                        return;
                                    }
                                    if ((float) $value > (float) $ar->balance) {
                                        $fail("Jumlah pembayaran ({$value}) melebihi sisa piutang ({$ar->balance}).");
                                    }
                                },
                            ]),
                        DatePicker::make('payment_date')
                            ->required()
                            ->default(now()),
                        Select::make('payment_method')
                            ->options([
                                'cash' => 'Tunai',
                                'transfer' => 'Transfer Bank',
                                'qris' => 'QRIS',
                                'debit' => 'Kartu Debit',
                                'credit_card' => 'Kartu Kredit',
                            ])
                            ->required()
                            ->default('cash'),
                        TextInput::make('reference')
                            ->label('Referensi')
                            ->placeholder('No. referensi transfer / ID transaksi')
                            ->maxLength(255),
                        Textarea::make('notes')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('accountReceivable.ar_number')
                    ->label('Piutang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('accountReceivable.customer.name')
                    ->label('Pelanggan')
                    ->searchable(),
                TextColumn::make('amount')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('payment_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'Tunai',
                        'transfer' => 'Transfer',
                        'qris' => 'QRIS',
                        'debit' => 'Debit',
                        'credit_card' => 'Kredit',
                        default => $state,
                    }),
                TextColumn::make('reference')
                    ->label('Referensi')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Tunai',
                        'transfer' => 'Transfer Bank',
                        'qris' => 'QRIS',
                        'debit' => 'Kartu Debit',
                        'credit_card' => 'Kartu Kredit',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArPayments::route('/'),
            'create' => CreateArPayment::route('/create'),
            'edit' => EditArPayment::route('/{record}/edit'),
        ];
    }
}
