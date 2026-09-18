<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama',
        'kategori',
        'menu_category_id',
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

    protected $appends = [
        'kategori',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id');
    }

    public function getKategoriAttribute(): ?string
    {
        if ($this->relationLoaded('category')) {
            return $this->category?->slug;
        }

        return null;
    }

    public function setKategoriAttribute(?string $value): void
    {
        if (! empty($value)) {
            $category = MenuCategory::firstOrCreate(
                ['slug' => $value],
                ['nama' => ucwords(str_replace('-', ' ', $value))]
            );
            $this->attributes['menu_category_id'] = $category->id;
        }
    }

    public function branchPrices(): HasMany
    {
        return $this->hasMany(BranchMenuPrice::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function formatHarga(int|float $price): string
    {
        return 'Rp '.number_format((int) $price, 0, ',', '.');
    }
}
