<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArPayment extends Model
{
    protected $fillable = [
        'store_id',
        'account_receivable_id',
        'amount',
        'payment_date',
        'payment_method',
        'reference',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function accountReceivable(): BelongsTo
    {
        return $this->belongsTo(AccountReceivable::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function booted(): void
    {
        static::deleted(function (ArPayment $payment) {
            $ar = $payment->accountReceivable;

            if (! $ar) {
                return;
            }

            $totalPaid = (float) $ar->payments()->sum('amount');
            $totalAmount = (float) $ar->total_amount;

            $status = match (true) {
                $totalPaid >= $totalAmount => 'paid',
                $totalPaid > 0 => 'partial',
                default => 'unpaid',
            };

            $ar->update([
                'amount_paid' => $totalPaid,
                'status' => $status,
            ]);
        });
    }
}
