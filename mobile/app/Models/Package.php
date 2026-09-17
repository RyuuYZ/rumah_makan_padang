<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    const STATUSES = ['active', 'inactive'];

    public function items(): HasMany
    {
        return $this->hasMany(PackageItem::class);
    }

    public function menuItems()
    {
        return $this->belongsToMany(MenuItem::class, 'package_items')
            ->withPivot('quantity', 'is_optional')
            ->withTimestamps();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
