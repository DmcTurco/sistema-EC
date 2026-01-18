<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'staff_id',
        'product_id',
        'intro_video_path',
        'thumbnail_path',
        'status',
        'views',
        'sales',
    ];

    protected $casts = [
        'status' => 'integer',
        'views' => 'integer',
        'sales' => 'integer',
    ];

    // Constantes para status
    const STATUS_PRIVATE = 0;
    const STATUS_PUBLIC = 1;

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('status', self::STATUS_PUBLIC);
    }

    public function scopePrivate($query)
    {
        return $query->where('status', self::STATUS_PRIVATE);
    }

    public function scopeByStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopePopular($query, $limit = 10)
    {
        return $query->orderBy('views', 'desc')->limit($limit);
    }

    public function scopeTopSales($query, $limit = 10)
    {
        return $query->orderBy('sales', 'desc')->limit($limit);
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        return $this->status === self::STATUS_PUBLIC ? 'Público' : 'Privado';
    }

    public function getStatusBadgeAttribute()
    {
        return $this->status === self::STATUS_PUBLIC 
            ? '<span class="badge badge-sm bg-gradient-success">Público</span>' 
            : '<span class="badge badge-sm bg-gradient-secondary">Privado</span>';
    }

    public function getIntroVideoUrlAttribute()
    {
        if (!$this->intro_video_path) {
            return null;
        }

        // Si es una URL completa
        if (filter_var($this->intro_video_path, FILTER_VALIDATE_URL)) {
            return $this->intro_video_path;
        }

        // Si está en storage
        return Storage::url($this->intro_video_path);
    }

    public function getThumbnailUrlAttribute()
    {
        if (!$this->thumbnail_path) {
            return asset('assets/img/default-thumbnail.jpg');
        }

        if (filter_var($this->thumbnail_path, FILTER_VALIDATE_URL)) {
            return $this->thumbnail_path;
        }

        return Storage::url($this->thumbnail_path);
    }

    // Relaciones
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Métodos útiles
    public function incrementViews()
    {
        $this->increment('views');
    }

    public function recordSale()
    {
        $this->increment('sales');
    }

    public function isPublic()
    {
        return $this->status === self::STATUS_PUBLIC;
    }

    public function isPrivate()
    {
        return $this->status === self::STATUS_PRIVATE;
    }

    public function toggleStatus()
    {
        $this->status = $this->status === self::STATUS_PUBLIC 
            ? self::STATUS_PRIVATE 
            : self::STATUS_PUBLIC;
        return $this->save();
    }

    public function getCombinedVideoDataAttribute()
    {
        return [
            'intro_video' => $this->intro_video_url,
            'main_video' => $this->product->main_video_url ?? null,
            'product_name' => $this->product->name,
            'product_price' => $this->product->formatted_price,
        ];
    }
}