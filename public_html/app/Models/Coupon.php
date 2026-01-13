<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'discount_amount',
        'status',
        'expired_at',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'expired_at' => 'datetime',
    ];
}
