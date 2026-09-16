<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'cashier_id',
        'method',
        'amount',
        'cash_given',
        'change_amount',
        'reference_number',
        'status',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'cash_given' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    const METHODS = ['cash', 'qris'];

    const STATUSES = ['pending', 'completed', 'failed', 'voided'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCash(): bool
    {
        return $this->method === 'cash';
    }
}
