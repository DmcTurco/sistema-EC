<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class General extends Authenticatable
{
     use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'preferred_store_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'preferred_store_id' => 'integer',
    ];

    // Relaciones
    public function preferredStore()
    {
        return $this->belongsTo(Store::class, 'preferred_store_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'user_coupons')
            ->withPivot('order_id', 'status', 'used_at')
            ->withTimestamps();
    }

    public function availableCoupons()
    {
        return $this->belongsToMany(Coupon::class, 'user_coupons')
            ->wherePivot('status', 0)
            ->withPivot('order_id', 'status', 'used_at')
            ->withTimestamps();
    }

    public function usedCoupons()
    {
        return $this->belongsToMany(Coupon::class, 'user_coupons')
            ->wherePivot('status', 1)
            ->withPivot('order_id', 'status', 'used_at')
            ->withTimestamps();
    }

    // Accessors
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
        ]);

        return implode(', ', $parts);
    }

    public function getHasCompleteAddressAttribute()
    {
        return !empty($this->address) && !empty($this->city);
    }

    // Métodos útiles
    public function hasPreferredStore()
    {
        return !is_null($this->preferred_store_id);
    }

    public function grantCoupon($couponId, $orderId)
    {
        $this->coupons()->attach($couponId, [
            'order_id' => $orderId,
            'status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function markCouponAsUsed($userCouponId)
    {
        return \DB::table('user_coupons')
            ->where('id', $userCouponId)
            ->where('user_id', $this->id)
            ->update([
                'status' => 1,
                'used_at' => now(),
                'updated_at' => now(),
            ]);
    }
}
