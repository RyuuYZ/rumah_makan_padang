<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'cashier_id',
        'order_number',
        'status',
        'order_type',
        'table_number',
        'qr_code_token',
        'source',
        'method',
        'total',
        'payment_status',
        'customer_name',
        'customer_phone',
        'notes',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    const STATUSES = ['pending', 'confirmed', 'cooking', 'ready', 'completed', 'cancelled'];

    const METHODS = ['dine-in', 'online', 'delivery'];

    const ORDER_TYPES = ['dine_in', 'takeaway'];

    const SOURCES = ['pos', 'customer_web', 'qr_scan'];

    const PAYMENT_STATUSES = ['unpaid', 'paid', 'voided'];

    /**
     * Auto-generate order_number dan qr_code_token saat order dibuat.
     */
    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $order->order_number = 'RM-'.strtoupper(Str::random(6)).'-'.now()->format('dmY');
            }
            if (empty($order->qr_code_token)) {
                $order->qr_code_token = Str::uuid()->toString();
            }
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }
}
