<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'table_id',
        'customer_name',
        'customer_phone',
        'reservation_time',
        'guest_count',
        'status',
        'notes',
    ];

    protected $casts = [
        'reservation_time' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
