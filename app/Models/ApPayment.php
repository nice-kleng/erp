<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApPayment extends Model
{
    protected $fillable = [
        'store_id',
        'account_payable_id',
        'amount',
        'payment_date',
        'payment_method',
        'reference',
        'notes',
        'created_by',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function accountPayable(): BelongsTo
    {
        return $this->belongsTo(AccountPayable::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::deleted(function (ApPayment $payment) {
            $ap = $payment->accountPayable;
            if (! $ap) {
                return;
            }

            $totalPaid = (float) $ap->payments()->sum('amount');
            $totalAmount = (float) $ap->total_amount;

            $status = match (true) {
                $totalPaid >= $totalAmount => 'paid',
                $totalPaid > 0 => 'partial',
                default => 'unpaid',
            };

            $ap->update([
                'amount_paid' => $totalPaid,
                'status' => $status,
            ]);
        });
    }
}
