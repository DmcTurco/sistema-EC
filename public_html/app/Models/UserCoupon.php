<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCoupon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'coupon_id',
        'status',   // 0=no usado, 1=usado
        'used_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];
}
