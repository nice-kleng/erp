<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionIngredient extends Model
{
    protected $fillable = [
        'production_id',
        'product_id',
        'product_variant_id',
        'qty_required',
        'qty_used',
        'unit_price',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'qty_required' => 'decimal:2',
            'qty_used' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function production(): BelongsTo
    {
        return $this->belongsTo(Production::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
