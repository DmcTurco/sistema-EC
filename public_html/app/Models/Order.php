<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number',
        'general_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'city',
        'state',
        'postal_code',
        'total_amount',
        'status',
        'referral_post_id',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'status' => 'integer',
        'general_id' => 'integer',
        'referral_post_id' => 'integer',
    ];

    // Constantes para status
    const STATUS_PENDING = 0;
    const STATUS_PAID = 1;
    const STATUS_SHIPPED = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_CANCELLED = 9;

    // Boot method para generar número de orden
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(10));
            }
        });
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopeShipped($query)
    {
        return $query->where('status', self::STATUS_SHIPPED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeByGeneral($query, $generalId)
    {
        return $query->where('general_id', $generalId);
    }

    public function scopeByPost($query, $postId)
    {
        return $query->where('referral_post_id', $postId);
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_PAID => 'Pagado',
            self::STATUS_SHIPPED => 'Enviado',
            self::STATUS_COMPLETED => 'Completado',
            self::STATUS_CANCELLED => 'Cancelado',
            default => 'Desconocido',
        };
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING => '<span class="badge badge-sm bg-gradient-warning">Pendiente</span>',
            self::STATUS_PAID => '<span class="badge badge-sm bg-gradient-info">Pagado</span>',
            self::STATUS_SHIPPED => '<span class="badge badge-sm bg-gradient-primary">Enviado</span>',
            self::STATUS_COMPLETED => '<span class="badge badge-sm bg-gradient-success">Completado</span>',
            self::STATUS_CANCELLED => '<span class="badge badge-sm bg-gradient-danger">Cancelado</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge badge-sm bg-gradient-secondary">Desconocido</span>';
    }

    public function getFormattedTotalAttribute()
    {
        return 'S/ ' . number_format($this->total_amount, 2);
    }

    public function getFullShippingAddressAttribute()
    {
        $parts = array_filter([
            $this->shipping_address,
            $this->city,
            $this->state,
            $this->postal_code,
        ]);

        return implode(', ', $parts);
    }

    // Relaciones
    public function general()
    {
        return $this->belongsTo(General::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function referralPost()
    {
        return $this->belongsTo(Post::class, 'referral_post_id');
    }

    // Métodos útiles
    public function markAsPaid()
    {
        $this->update(['status' => self::STATUS_PAID]);
    }

    public function markAsShipped()
    {
        $this->update(['status' => self::STATUS_SHIPPED]);
    }

    public function markAsCompleted()
    {
        $this->update(['status' => self::STATUS_COMPLETED]);
    }

    public function markAsCancelled()
    {
        $this->update(['status' => self::STATUS_CANCELLED]);
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isPaid()
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isShipped()
    {
        return $this->status === self::STATUS_SHIPPED;
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function getTotalItems()
    {
        return $this->items()->sum('quantity');
    }

    public function calculateTotal()
    {
        return $this->items()->sum('subtotal');
    }
}