<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;

class Product extends Model
{
    use SoftDeletes,Notifiable,SoftDeletes;

    protected $fillable = [
        'name', 
        'description', 
        'price', 
        'stock', 
        'sku', 
        'image', 
        'main_video_path', 
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'status' => 'integer',
    ];

    // Constantes para status
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%");
        });
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock', '<=', 0);
    }

    public function scopeLowStock($query, $threshold = 10)
    {
        return $query->where('stock', '<=', $threshold)->where('stock', '>', 0);
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        return $this->status === self::STATUS_ACTIVE ? 'Activo' : 'Inactivo';
    }

    public function getStatusBadgeAttribute()
    {
        return $this->status === self::STATUS_ACTIVE 
            ? '<span class="badge badge-success">Activo</span>' 
            : '<span class="badge badge-secondary">Inactivo</span>';
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return asset('images/no-image.png'); // Imagen por defecto
        }

        // Si es una URL completa
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        // Si está en storage público
        return Storage::url($this->image);
    }

    public function getMainVideoUrlAttribute()
    {
        if (!$this->main_video_path) {
            return null;
        }

        // Si es una URL completa
        if (filter_var($this->main_video_path, FILTER_VALIDATE_URL)) {
            return $this->main_video_path;
        }

        // Si está en storage
        return Storage::url($this->main_video_path);
    }

    public function getFormattedPriceAttribute()
    {
        return 'S/ ' . number_format($this->price, 2);
    }

    public function getStockStatusAttribute()
    {
        if ($this->stock <= 0) {
            return '<span class="badge badge-danger">Sin Stock</span>';
        } elseif ($this->stock <= 10) {
            return '<span class="badge badge-warning">Stock Bajo</span>';
        }
        return '<span class="badge badge-success">En Stock</span>';
    }

    public function getIsInStockAttribute()
    {
        return $this->stock > 0;
    }

    // Mutators
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = round($value, 2);
    }

    // Relaciones
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    // Métodos útiles
    public function decreaseStock($quantity)
    {
        if ($this->stock >= $quantity) {
            $this->stock -= $quantity;
            return $this->save();
        }
        return false;
    }

    public function increaseStock($quantity)
    {
        $this->stock += $quantity;
        return $this->save();
    }

    public function hasStock($quantity = 1)
    {
        return $this->stock >= $quantity;
    }
}