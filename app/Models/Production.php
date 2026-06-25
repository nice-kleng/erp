<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Production extends Model
{
    protected $fillable = [
        'store_id',
        'recipe_id',
        'production_number',
        'product_id',
        'qty_produced',
        'total_cost',
        'status',
        'notes',
        'produced_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'qty_produced' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'produced_at' => 'date',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(ProductionIngredient::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function booted(): void
    {
        static::deleted(function (Production $production) {
            StockMovement::where('reference_type', 'production')
                ->where('reference_id', $production->id)
                ->delete();
        });
    }
}
