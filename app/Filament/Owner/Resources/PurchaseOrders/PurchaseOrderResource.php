<?php

namespace App\Filament\Owner\Resources\PurchaseOrders;

use App\Filament\Owner\Resources\PurchaseOrders\Pages\CreatePurchaseOrder;
use App\Filament\Owner\Resources\PurchaseOrders\Pages\EditPurchaseOrder;
use App\Filament\Owner\Resources\PurchaseOrders\Pages\ListPurchaseOrders;
use App\Models\Product;
use App\Models\PurchaseOrder;
use Filament\Actions\Action;
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

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-shopping-cart';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Pembelian';
    }

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Pesanan')
                    ->schema([
                        Select::make('supplier_id')
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('order_number')
                            ->required()
                            ->maxLength(255)
                            ->default(fn () => 'PO-'.now()->format('Ymd').'-'.Str::upper(Str::random(4))),
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'ordered' => 'Dipesan',
                                'partially_received' => 'Diterima Sebagian',
                                'received' => 'Diterima',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->default('draft')
                            ->required(),
                        DatePicker::make('ordered_at')
                            ->label('Tanggal Pesan'),
                        DatePicker::make('expected_at')
                            ->label('Estimasi Tiba'),
                        Textarea::make('notes')
                            ->rows(3),
                    ])->columns(2),

                Section::make('Item Pesanan')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->live()
                            ->afterStateUpdated(fn ($state, $set, $get, $livewire) => $livewire->syncRingkasan())
                            ->schema([
                                Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set, $get, $livewire) {
                                        $product = Product::find($state);
                                        if ($product) {
                                            $set('unit_price', $product->purchase_price);
                                            $set('subtotal', $product->purchase_price * ($get('qty_ordered') ?? 1));
                                        }
                                        $livewire->syncRingkasan();
                                    }),
                                TextInput::make('qty_ordered')
                                    ->label('Qty')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set, $get, $livewire) {
                                        $set('subtotal', ($state ?? 0) * ($get('unit_price') ?? 0));
                                        $livewire->syncRingkasan();
                                    }),
                                TextInput::make('unit_price')
                                    ->label('Harga')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set, $get, $livewire) {
                                        $set('subtotal', ($state ?? 0) * ($get('qty_ordered') ?? 1));
                                        $livewire->syncRingkasan();
                                    }),
                                TextInput::make('subtotal')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->readOnly(),
                            ])
                            ->columns(4)
                            ->defaultItems(1),
                    ]),

                Section::make('Ringkasan')
                    ->schema([
                        TextInput::make('subtotal')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->readOnly()
                            ->live(true),
                        TextInput::make('discount')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn ($state, $set, $get) => $set('total', ((float) ($get('subtotal') ?? 0)) - ((float) ($state ?? 0)) + ((float) ($get('tax') ?? 0)))
                            ),
                        TextInput::make('tax')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn ($state, $set, $get) => $set('total', ((float) ($get('subtotal') ?? 0)) - ((float) ($get('discount') ?? 0)) + ((float) ($state ?? 0)))
                            ),
                        TextInput::make('total')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->readOnly(),
                    ])->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('supplier.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'ordered' => 'warning',
                        'partially_received' => 'info',
                        'received' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'ordered' => 'Dipesan',
                        'partially_received' => 'Diterima Sebagian',
                        'received' => 'Diterima',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    }),
                TextColumn::make('total')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('ordered_at')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('expected_at')
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
                        'ordered' => 'Dipesan',
                        'partially_received' => 'Diterima Sebagian',
                        'received' => 'Diterima',
                        'cancelled' => 'Dibatalkan',
                    ]),
            ])
            ->recordActions([
                Action::make('markAsOrdered')
                    ->label('Kirim PO')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->visible(fn (PurchaseOrder $record): bool => $record->status === 'draft')
                    ->requiresConfirmation()
                    ->modalHeading('Kirim Purchase Order')
                    ->modalDescription('Apakah Anda yakin ingin mengirim PO ini ke supplier? Status akan berubah menjadi "Dipesan".')
                    ->action(fn (PurchaseOrder $record) => $record->update([
                        'status' => 'ordered',
                        'ordered_at' => now(),
                    ])),
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
            'index' => ListPurchaseOrders::route('/'),
            'create' => CreatePurchaseOrder::route('/create'),
            'edit' => EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
