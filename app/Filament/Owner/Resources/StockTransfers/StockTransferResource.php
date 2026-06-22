<?php

namespace App\Filament\Owner\Resources\StockTransfers;

use App\Filament\Owner\Resources\StockTransfers\Pages\ManageStockTransfers;
use App\Models\StockTransfer;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
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

class StockTransferResource extends Resource
{
    protected static ?string $model = StockTransfer::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-arrows-right-left';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Inventaris';
    }

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Transfer')
                    ->schema([
                        Select::make('from_store_id')
                            ->relationship('fromStore', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Dari Toko'),
                        Select::make('to_store_id')
                            ->relationship('toStore', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Ke Toko'),
                        TextInput::make('transfer_number')
                            ->required()
                            ->maxLength(255)
                            ->default(fn () => 'TRF-'.now()->format('Ymd').'-'.Str::upper(Str::random(4))),
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'in_transit' => 'Dalam Perjalanan',
                                'received' => 'Diterima',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->default('draft')
                            ->required(),
                        DatePicker::make('sent_at')
                            ->label('Tanggal Kirim'),
                        DatePicker::make('received_at')
                            ->label('Tanggal Terima'),
                        Textarea::make('notes')
                            ->rows(3),
                    ])->columns(2),

                Section::make('Item Transfer')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('qty')
                                    ->label('Qty')
                                    ->required()
                                    ->numeric()
                                    ->default(1),
                            ])
                            ->columns(2)
                            ->defaultItems(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transfer_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fromStore.name')
                    ->label('Dari')
                    ->sortable(),
                TextColumn::make('toStore.name')
                    ->label('Ke')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'in_transit' => 'warning',
                        'received' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'in_transit' => 'Dalam Perjalanan',
                        'received' => 'Diterima',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    }),
                TextColumn::make('sent_at')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('received_at')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'in_transit' => 'Dalam Perjalanan',
                        'received' => 'Diterima',
                        'cancelled' => 'Dibatalkan',
                    ]),
            ])
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
            'index' => ManageStockTransfers::route('/'),
        ];
    }
}
