<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Coupon extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image_path',
        'min_purchase',
        'status',
    ];

    protected $casts = [
        'min_purchase' => 'decimal:2',
        'status' => 'integer',
    ];

    // Constantes
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    // Accessors
    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return asset('assets/img/default-coupon.jpg');
        }

        if (filter_var($this->image_path, FILTER_VALIDATE_URL)) {
            return $this->image_path;
        }

        return Storage::url($this->image_path);
    }

    public function getFormattedMinPurchaseAttribute()
    {
        return 'S/ ' . number_format($this->min_purchase, 2);
    }

    // Relaciones
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_coupons')
            ->withPivot('order_id', 'status', 'used_at')
            ->withTimestamps();
    }

    // Métodos
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function canBeGrantedForOrder($orderAmount)
    {
        return $orderAmount >= $this->min_purchase;
    }
}