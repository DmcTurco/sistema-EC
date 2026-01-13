<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'total_amount',
        'status',           // 0=pending,1=paid,2=shipped,3=completed
        'referral_post_id', // video/post
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];
}
