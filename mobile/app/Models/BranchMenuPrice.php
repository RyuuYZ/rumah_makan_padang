<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchMenuPrice extends Model
{
    protected $fillable = [
        'branch_id',
        'menu_item_id',
        'harga',
        'is_available',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id')->withTrashed();
    }
}
