<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama',
        'kategori',
        'deskripsi',
        'foto',
        'badge',
        'rating',
        'is_active',
        'availability_status',
        'stock_quantity',
    ];

    protected $casts = [
        'rating' => 'decimal:1',
        'is_active' => 'boolean',
    ];

    public function branchPrices(): HasMany
    {
        return $this->hasMany(BranchMenuPrice::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function formatHarga(int|float $price): string
    {
        return 'Rp '.number_format((int) $price, 0, ',', '.');
    }
}
