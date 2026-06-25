<?php

namespace App\Filament\Owner\Resources\Productions;

use App\Filament\Owner\Resources\Productions\Pages\CreateProduction;
use App\Filament\Owner\Resources\Productions\Pages\EditProduction;
use App\Filament\Owner\Resources\Productions\Pages\ListProductions;
use App\Models\Production;
use App\Models\Recipe;
use Filament\Actions\DeleteAction;
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

class ProductionResource extends Resource
{
    protected static ?string $model = Production::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cog';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Produksi';
    }

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Produksi')
                    ->schema([
                        TextInput::make('production_number')
                            ->required()
                            ->maxLength(255)
                            ->default(fn () => 'PRD-'.now()->format('Ymd').'-'.Str::upper(Str::random(4))),
                        Select::make('recipe_id')
                            ->relationship('recipe', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Resep')
                            ->live(true)
                            ->afterStateUpdated(function ($state, $set, $get, $livewire) {
                                if (! $state) {
                                    return;
                                }

                                $recipe = Recipe::with('ingredients.product', 'ingredients.productVariant')->find($state);
                                if (! $recipe) {
                                    return;
                                }

                                $set('product_id', $recipe->product_id);
                                $qtyProduced = (float) ($get('qty_produced') ?? 1);

                                $items = [];
                                foreach ($recipe->ingredients as $ingredient) {
                                    $qtyReq = (float) $ingredient->qty * $qtyProduced;
                                    $unitPrice = (float) ($ingredient->product->purchase_price ?? 0);
                                    $items[] = [
                                        'product_id' => $ingredient->product_id,
                                        'product_variant_id' => $ingredient->product_variant_id,
                                        'qty_required' => $qtyReq,
                                        'qty_used' => $qtyReq,
                                        'unit_price' => $unitPrice,
                                        'subtotal' => $qtyReq * $unitPrice,
                                    ];
                                }

                                $livewire->data['ingredients'] = $items;
                            }),
                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->label('Produk Jadi'),
                        TextInput::make('qty_produced')
                            ->label('Qty Produksi')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->live(true)
                            ->afterStateUpdated(function ($state, $set, $get, $livewire) {
                                $recipeId = $get('recipe_id');
                                if (! $recipeId) {
                                    return;
                                }

                                $recipe = Recipe::with('ingredients')->find($recipeId);
                                if (! $recipe) {
                                    return;
                                }

                                $qtyProduced = (float) ($state ?? 1);
                                $items = $livewire->data['ingredients'] ?? [];

                                foreach ($items as $i => $item) {
                                    $itemId = $item['product_id'] ?? 0;
                                    $ingredient = $recipe->ingredients->firstWhere('product_id', $itemId);
                                    if ($ingredient) {
                                        $qtyReq = (float) $ingredient->qty * $qtyProduced;
                                        $items[$i]['qty_required'] = $qtyReq;
                                        $items[$i]['qty_used'] = $qtyReq;
                                        $items[$i]['subtotal'] = $qtyReq * (float) ($items[$i]['unit_price'] ?? 0);
                                    }
                                }

                                $livewire->data['ingredients'] = $items;
                            }),
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'completed' => 'Selesai',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->default('draft')
                            ->required(),
                        DatePicker::make('produced_at')
                            ->required()
                            ->default(now()),
                        Textarea::make('notes')
                            ->rows(3),
                    ])->columns(2),

                Section::make('Bahan Baku')
                    ->schema([
                        Repeater::make('ingredients')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->disabled()
                                    ->dehydrated()
                                    ->label('Bahan'),
                                Hidden::make('product_variant_id'),
                                TextInput::make('qty_required')
                                    ->label('Dibutuhkan')
                                    ->numeric()
                                    ->readOnly()
                                    ->disabled()
                                    ->dehydrated(),
                                TextInput::make('qty_used')
                                    ->label('Digunakan')
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->live(true)
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        $price = (float) ($get('unit_price') ?? 0);
                                        $set('subtotal', (float) $state * $price);
                                    }),
                                TextInput::make('unit_price')
                                    ->label('Harga Satuan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        $used = (float) ($get('qty_used') ?? 0);
                                        $set('subtotal', $used * (float) $state);
                                    }),
                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->readOnly()
                                    ->prefix('Rp'),
                            ])
                            ->columns(4),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('production_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('recipe.name')
                    ->label('Resep')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('qty_produced')
                    ->label('Hasil')
                    ->sortable(),
                TextColumn::make('total_cost')
                    ->label('Biaya')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    }),
                TextColumn::make('produced_at')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductions::route('/'),
            'create' => CreateProduction::route('/create'),
            'edit' => EditProduction::route('/{record}/edit'),
        ];
    }
}
