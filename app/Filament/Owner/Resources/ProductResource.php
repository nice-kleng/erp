<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cube';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Produk')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Textarea::make('description')
                            ->rows(3),
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('unit_id')
                            ->relationship('unit', 'name')
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                Section::make('Harga & Stok')
                    ->schema([
                        TextInput::make('purchase_price')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->maxValue(99999999999999),
                        TextInput::make('selling_price')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->maxValue(99999999999999),
                    ])->columns(2),

                Section::make('Identifikasi')
                    ->schema([
                        TextInput::make('sku')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('barcode')
                            ->maxLength(255),
                        Toggle::make('has_variants')
                            ->label('Produk memiliki varian (rasa/ukuran)')
                            ->reactive(),
                        Toggle::make('is_active')
                            ->default(true),
                    ])->columns(2),

                Section::make('Varian Produk')
                    ->schema([
                        Repeater::make('variants')
                            ->relationship()
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('sku')
                                    ->maxLength(255),
                                TextInput::make('barcode')
                                    ->maxLength(255),
                                TextInput::make('purchase_price')
                                    ->numeric()
                                    ->prefix('Rp'),
                                TextInput::make('selling_price')
                                    ->numeric()
                                    ->prefix('Rp'),
                                Toggle::make('is_active')
                                    ->default(true),
                            ])
                            ->columns(3)
                            ->hidden(fn ($get) => ! $get('has_variants')),
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
                TextColumn::make('category.name')
                    ->label('Category'),
                TextColumn::make('sku')
                    ->searchable(),
                TextColumn::make('selling_price')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('purchase_price')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('has_variants')
                    ->label('Varian')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->relationship('category', 'name'),
                TernaryFilter::make('is_active'),
                TernaryFilter::make('has_variants')
                    ->label('Has Variants'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
