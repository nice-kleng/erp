<?php

namespace App\Filament\Owner\Resources\GoodsReceipts;

use App\Filament\Owner\Resources\GoodsReceipts\Pages\CreateGoodsReceipt;
use App\Filament\Owner\Resources\GoodsReceipts\Pages\EditGoodsReceipt;
use App\Filament\Owner\Resources\GoodsReceipts\Pages\ListGoodsReceipts;
use App\Models\GoodsReceipt;
use App\Models\PurchaseOrderItem;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
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

class GoodsReceiptResource extends Resource
{
    protected static ?string $model = GoodsReceipt::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-arrow-down-tray';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Pembelian';
    }

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Penerimaan')
                    ->schema([
                        Select::make('purchase_order_id')
                            ->relationship('purchaseOrder', 'order_number', fn ($query) => $query->whereIn('status', ['ordered', 'partially_received'])
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Purchase Order')
                            ->live(true),
                        TextInput::make('receipt_number')
                            ->required()
                            ->maxLength(255)
                            ->default(fn () => 'GR-'.now()->format('Ymd').'-'.Str::upper(Str::random(4))),
                        DatePicker::make('received_at')
                            ->required()
                            ->default(now()),
                        Textarea::make('notes')
                            ->rows(3),
                    ])->columns(2),

                Section::make('Item Diterima')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Select::make('purchase_order_item_id')
                                    ->options(fn ($livewire) => $livewire->getAvailablePoItems())
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->label('PO Item')
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        $poItem = PurchaseOrderItem::with('product', 'productVariant')->find($state);
                                        if (! $poItem) {
                                            return;
                                        }

                                        $set('product_id', $poItem->product_id);
                                        $set('product_variant_id', $poItem->product_variant_id);
                                        $set('unit_price', $poItem->unit_price);
                                    }),
                                Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->disabled()
                                    ->dehydrated(),
                                Hidden::make('product_variant_id'),
                                TextInput::make('qty_received')
                                    ->label('Qty')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->rules([
                                        fn ($get, $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                            $poItemId = $get('purchase_order_item_id');
                                            if (! $poItemId) {
                                                return;
                                            }

                                            $poItem = PurchaseOrderItem::find($poItemId);
                                            if (! $poItem) {
                                                return;
                                            }

                                            $existingQty = 0;
                                            if ($record) {
                                                $currentItem = $record->items->firstWhere('purchase_order_item_id', $poItemId);
                                                if ($currentItem) {
                                                    $existingQty = (float) $currentItem->qty_received;
                                                }
                                            }

                                            $sisa = (float) $poItem->qty_ordered - (float) $poItem->qty_received + $existingQty;
                                            if ((float) $value > $sisa) {
                                                $fail("Qty diterima ({$value}) melebihi sisa ({$sisa}).");
                                            }
                                        },
                                    ]),
                                TextInput::make('unit_price')
                                    ->label('Harga')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(),
                            ])
                            ->columns(4)
                            ->defaultItems(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('receipt_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('purchaseOrder.order_number')
                    ->label('PO')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('received_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
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
            'index' => ListGoodsReceipts::route('/'),
            'create' => CreateGoodsReceipt::route('/create'),
            'edit' => EditGoodsReceipt::route('/{record}/edit'),
        ];
    }
}
