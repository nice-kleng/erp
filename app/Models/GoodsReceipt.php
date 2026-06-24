<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GoodsReceipt extends Model
{
    protected $fillable = [
        'store_id',
        'purchase_order_id',
        'receipt_number',
        'status',
        'notes',
        'received_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'received_at' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::deleted(function (GoodsReceipt $record) {
            StockMovement::where('reference_type', 'goods_receipt')
                ->where('reference_id', $record->id)
                ->delete();

            $record->accountPayable()?->delete();

            $po = $record->purchaseOrder;

            if ($po) {
                $po->load('items.goodsReceiptItems');

                foreach ($po->items as $poItem) {
                    $totalReceived = $poItem->goodsReceiptItems->sum('qty_received');
                    $poItem->update(['qty_received' => $totalReceived]);
                }

                $po->load('items');
                $totalOrdered = $po->items->sum('qty_ordered');
                $totalReceived = $po->items->sum('qty_received');

                match (true) {
                    $totalReceived >= $totalOrdered => $po->update(['status' => 'received']),
                    $totalReceived > 0 => $po->update(['status' => 'partially_received']),
                    $po->status !== 'cancelled' => $po->update(['status' => 'ordered']),
                    default => null,
                };
            }
        });
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function accountPayable(): HasOne
    {
        return $this->hasOne(AccountPayable::class);
    }
}
