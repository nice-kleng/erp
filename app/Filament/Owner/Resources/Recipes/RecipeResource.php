<?php

namespace App\Filament\Owner\Resources\Recipes;

use App\Filament\Owner\Resources\Recipes\Pages\CreateRecipe;
use App\Filament\Owner\Resources\Recipes\Pages\EditRecipe;
use App\Filament\Owner\Resources\Recipes\Pages\ListRecipes;
use App\Models\Product;
use App\Models\Recipe;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RecipeResource extends Resource
{
    protected static ?string $model = Recipe::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-beaker';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Produksi';
    }

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Resep')
                    ->schema([
                        Select::make('product_id')
                            ->relationship('product', 'name', fn ($query) => $query->where('type', 'finished'))
                            ->label('Produk Jadi')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn ($state, $set) => $set('name', Product::find($state)?->name ?? '')),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('qty_produced')
                            ->label('Hasil Per Resep')
                            ->numeric()
                            ->required()
                            ->default(1),
                        Textarea::make('notes')
                            ->rows(3),
                    ])->columns(2),

                Section::make('Bahan Baku')
                    ->schema([
                        Repeater::make('ingredients')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->relationship('product', 'name', fn ($query) => $query->where('type', 'raw'))
                                    ->label('Bahan Baku')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(fn ($state, $set) => $set('product_variant_id', null)),
                                Select::make('product_variant_id')
                                    ->label('Varian')
                                    ->relationship('productVariant', 'name')
                                    ->hidden(fn ($get) => ! $get('product_id')),
                                TextInput::make('qty')
                                    ->label('Takaran')
                                    ->required()
                                    ->numeric()
                                    ->default(1),
                            ])
                            ->columns(3)
                            ->defaultItems(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('Produk Jadi'),
                TextColumn::make('ingredients_count')
                    ->label('Jumlah Bahan')
                    ->sortable()
                    ->counts('ingredients'),
                TextColumn::make('qty_produced')
                    ->label('Hasil'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRecipes::route('/'),
            'create' => CreateRecipe::route('/create'),
            'edit' => EditRecipe::route('/{record}/edit'),
        ];
    }
}
