<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'branch_id',
        'order_id',
        'menu_item_id',
        'nama_pelanggan',
        'rating',
        'komentar',
        'is_approved',
        'is_pinned',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
        'is_pinned' => 'boolean',
    ];

    public function branch(): ?BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function getAverageRatingAttribute(): float
    {
        return $this->branch()?->reviews()->where('is_approved', true)->avg('rating') ?? 0;
    }
}
