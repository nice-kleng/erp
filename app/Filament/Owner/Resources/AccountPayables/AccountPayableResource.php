<?php

namespace App\Filament\Owner\Resources\AccountPayables;

use App\Filament\Owner\Resources\AccountPayables\Pages\CreateAccountPayable;
use App\Filament\Owner\Resources\AccountPayables\Pages\EditAccountPayable;
use App\Filament\Owner\Resources\AccountPayables\Pages\ListAccountPayables;
use App\Models\AccountPayable;
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
use Illuminate\Support\Str;

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Hutang')
                    ->schema([
                        Select::make('supplier_id')
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('ap_number')
                            ->required()
                            ->maxLength(255)
                            ->default(fn () => 'AP-'.now()->format('Ymd').'-'.Str::upper(Str::random(4))),
                        Select::make('goods_receipt_id')
                            ->relationship('goodsReceipt', 'receipt_number')
                            ->searchable()
                            ->preload()
                            ->label('Goods Receipt'),
                        TextInput::make('total_amount')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->maxValue(99999999999999),
                        TextInput::make('amount_paid')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->maxValue(99999999999999),
                        DatePicker::make('due_date')
                            ->required()
                            ->default(now()->addDays(30)),
                        Select::make('status')
                            ->options([
                                'unpaid' => 'Belum Dibayar',
                                'partial' => 'Dibayar Sebagian',
                                'paid' => 'Lunas',
                            ])
                            ->required()
                            ->default('unpaid'),
                        Textarea::make('notes')
                            ->rows(3),
                    ])->columns(2),
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
            'index' => ListAccountPayables::route('/'),
            'create' => CreateAccountPayable::route('/create'),
            'edit' => EditAccountPayable::route('/{record}/edit'),
        ];
    }
}
