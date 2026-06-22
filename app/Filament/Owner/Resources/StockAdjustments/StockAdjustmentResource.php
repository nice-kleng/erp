<?php

namespace App\Filament\Owner\Resources\StockAdjustments;

use App\Filament\Owner\Resources\StockAdjustments\Pages\ManageStockAdjustments;
use App\Models\StockAdjustment;
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

class StockAdjustmentResource extends Resource
{
    protected static ?string $model = StockAdjustment::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-clipboard-document-list';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Inventaris';
    }

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Opname')
                    ->schema([
                        TextInput::make('adjustment_number')
                            ->required()
                            ->maxLength(255)
                            ->default(fn () => 'ADJ-'.now()->format('Ymd').'-'.Str::upper(Str::random(4))),
                        Select::make('type')
                            ->options([
                                'in' => 'Masuk (Penambahan)',
                                'out' => 'Keluar (Pengurangan)',
                                'opname' => 'Opname Stok',
                            ])
                            ->default('opname')
                            ->required(),
                        DatePicker::make('adjusted_at')
                            ->required()
                            ->default(now()),
                        Textarea::make('notes')
                            ->rows(3),
                    ])->columns(2),

                Section::make('Item Adjustment')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->label('Produk'),
                                TextInput::make('expected_qty')
                                    ->label('Stok Sistem')
                                    ->numeric(),
                                TextInput::make('actual_qty')
                                    ->label('Stok Fisik')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        $expected = (float) ($get('expected_qty') ?? 0);
                                        $set('difference', (float) $state - $expected);
                                    }),
                                TextInput::make('difference')
                                    ->label('Selisih')
                                    ->numeric()
                                    ->readOnly(),
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
                TextColumn::make('adjustment_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'danger',
                        'opname' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'in' => 'Masuk',
                        'out' => 'Keluar',
                        'opname' => 'Opname',
                        default => $state,
                    }),
                TextColumn::make('adjusted_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'in' => 'Masuk',
                        'out' => 'Keluar',
                        'opname' => 'Opname',
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
            'index' => ManageStockAdjustments::route('/'),
        ];
    }
}
