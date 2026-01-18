<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class Staff extends Authenticatable
{
    use SoftDeletes, HasFactory, Notifiable;

    protected $table = 'staff';

    protected $fillable = ['name', 'email', 'password', 'store_id', 'status'];

    protected $hidden = ['password'];

    protected $casts = [
        'status' => 'integer',
        'store_id' => 'integer',
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
            $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
        });
    }

    public function scopeByStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        return $this->status === self::STATUS_ACTIVE ? 'Activo' : 'Inactivo';
    }

    public function getStatusBadgeAttribute()
    {
        return $this->status === self::STATUS_ACTIVE ? '<span class="badge badge-sm bg-gradient-success">Activo</span>' : '<span class="badge badge-sm bg-gradient-secondary">Inactivo</span>';
    }

    // Mutators
    public function setPasswordAttribute($value)
    {
        // Solo hashear si no está ya hasheado
        if ($value && !str_starts_with($value, '$2y$')) {
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    // Relaciones
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // Métodos útiles
    public function hasStore()
    {
        return !is_null($this->store_id);
    }

    public function getStoreNameAttribute()
    {
        return $this->store ? $this->store->name : 'Sin tienda';
    }
}
