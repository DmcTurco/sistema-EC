<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Store extends Model
{
    use HasFactory, SoftDeletes,Notifiable;

    protected $fillable = ['name', 'address', 'status'];

    protected $casts = [
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
            $q->where('name', 'like', "%{$search}%")->orWhere('address', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        return $this->status === self::STATUS_ACTIVE ? 'Activo' : 'Inactivo';
    }

    public function getStatusBadgeAttribute()
    {
        return $this->status === self::STATUS_ACTIVE ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-secondary">Inactivo</span>';
    }
}
