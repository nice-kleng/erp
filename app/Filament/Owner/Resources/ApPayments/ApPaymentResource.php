<?php

namespace App\Filament\Owner\Resources\ApPayments;

use App\Filament\Owner\Resources\ApPayments\Pages\CreateApPayment;
use App\Filament\Owner\Resources\ApPayments\Pages\EditApPayment;
use App\Filament\Owner\Resources\ApPayments\Pages\ListApPayments;
use App\Models\ApPayment;
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

class ApPaymentResource extends Resource
{
    protected static ?string $model = ApPayment::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-banknotes';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Pembelian';
    }

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Pembayaran')
                    ->schema([
                        Select::make('account_payable_id')
                            ->relationship('accountPayable', 'ap_number')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Hutang'),
                        TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->maxValue(99999999999999),
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
                            ->maxLength(255),
                        Textarea::make('notes')
                            ->rows(3),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('accountPayable.ap_number')
                    ->label('Hutang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('accountPayable.supplier.name')
                    ->label('Supplier')
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
                TextColumn::make('notes')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => ListApPayments::route('/'),
            'create' => CreateApPayment::route('/create'),
            'edit' => EditApPayment::route('/{record}/edit'),
        ];
    }
}
