<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'order_id' => 'integer',
        'product_id' => 'integer',
    ];

    // Boot method para calcular subtotal automáticamente
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->subtotal = $item->quantity * $item->price;
        });
    }

    // Relaciones
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Accessors
    public function getFormattedPriceAttribute()
    {
        return 'S/ ' . number_format($this->price, 2);
    }

    public function getFormattedSubtotalAttribute()
    {
        return 'S/ ' . number_format($this->subtotal, 2);
    }

    // Métodos útiles
    public function updateQuantity($quantity)
    {
        $this->quantity = $quantity;
        $this->subtotal = $this->quantity * $this->price;
        return $this->save();
    }
}