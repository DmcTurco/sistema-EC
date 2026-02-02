<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = ['staff_id', 'product_id', 'intro_video_path', 'thumbnail_path', 'status', 'views', 'sales'];

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

    // ===== ACCESSORS =====

    /**
     * Status como texto
     */
    public function getStatusTextAttribute()
    {
        return $this->status === self::STATUS_PUBLIC ? 'Público' : 'Privado';
    }

    /**
     * Badge HTML del status
     */
    public function getStatusBadgeAttribute()
    {
        return $this->status === self::STATUS_PUBLIC ? '<span class="badge badge-sm bg-gradient-success">Público</span>' : '<span class="badge badge-sm bg-gradient-secondary">Privado</span>';
    }

    /**
     * URL completa del video de introducción
     */
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
        return asset('storage/' . $this->intro_video_path);
    }

    /**
     * ✅ URL completa del thumbnail (con fallback)
     */
    public function getThumbnailUrlAttribute()
    {
        if (!$this->thumbnail_path) {
            // ✅ Fallback: Si no hay thumbnail, usar placeholder
            return asset('assets/img/placeholder-video.jpg');
        }

        if (filter_var($this->thumbnail_path, FILTER_VALIDATE_URL)) {
            return $this->thumbnail_path;
        }

        return asset('storage/' . $this->thumbnail_path);
    }

    /**
     * ✅ Verificar si el post tiene video
     */
    public function getHasVideoAttribute()
    {
        return !empty($this->intro_video_path);
    }

    /**
     * ✅ Verificar si el post tiene thumbnail
     */
    public function getHasThumbnailAttribute()
    {
        return !empty($this->thumbnail_path);
    }

    /**
     * ✅ Nombre del archivo de video (sin path)
     */
    public function getVideoFileNameAttribute()
    {
        if (!$this->intro_video_path) {
            return null;
        }

        return basename($this->intro_video_path);
    }

    /**
     * ✅ Tamaño del archivo de video (si existe)
     */
    public function getVideoFileSizeAttribute()
    {
        if (!$this->intro_video_path) {
            return null;
        }

        $path = storage_path('app/public/' . $this->intro_video_path);

        if (file_exists($path)) {
            return filesize($path);
        }

        return null;
    }

    /**
     * ✅ Tamaño formateado del video
     */
    public function getFormattedVideoSizeAttribute()
    {
        $bytes = $this->video_file_size;

        if (!$bytes) {
            return 'Desconocido';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Datos combinados del video (intro + producto)
     */
    public function getCombinedVideoDataAttribute()
    {
        return [
            'intro_video' => $this->intro_video_url,
            'intro_thumbnail' => $this->thumbnail_url,
            'main_video' => $this->product->main_video_url ?? null,
            'main_thumbnail' => $this->product->video_thumbnail_url ?? null,
            'product_name' => $this->product->name,
            'product_price' => $this->product->formatted_price ?? 'N/A',
            'staff_name' => $this->staff->name ?? 'N/A',
        ];
    }

    // ===== RELACIONES =====

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ===== MÉTODOS ÚTILES =====

    /**
     * Incrementar vistas
     */
    public function incrementViews()
    {
        $this->increment('views');
    }

    /**
     * Registrar venta
     */
    public function recordSale()
    {
        $this->increment('sales');
    }

    /**
     * Verificar si es público
     */
    public function isPublic()
    {
        return $this->status === self::STATUS_PUBLIC;
    }

    /**
     * Verificar si es privado
     */
    public function isPrivate()
    {
        return $this->status === self::STATUS_PRIVATE;
    }

    /**
     * Alternar estado público/privado
     */
    public function toggleStatus()
    {
        $this->status = $this->status === self::STATUS_PUBLIC ? self::STATUS_PRIVATE : self::STATUS_PUBLIC;
        return $this->save();
    }

    /**
     * ✅ Eliminar archivos asociados (video + thumbnail)
     */
    public function deleteFiles()
    {
        $deleted = [];

        // Eliminar video
        if ($this->intro_video_path && Storage::disk('public')->exists($this->intro_video_path)) {
            Storage::disk('public')->delete($this->intro_video_path);
            $deleted[] = 'video';
        }

        // Eliminar thumbnail
        if ($this->thumbnail_path && Storage::disk('public')->exists($this->thumbnail_path)) {
            Storage::disk('public')->delete($this->thumbnail_path);
            $deleted[] = 'thumbnail';
        }

        return $deleted;
    }

    /**
     * ✅ Verificar si los archivos existen físicamente
     */
    public function filesExist()
    {
        $videoExists = $this->intro_video_path && Storage::disk('public')->exists($this->intro_video_path);
        $thumbnailExists = $this->thumbnail_path && Storage::disk('public')->exists($this->thumbnail_path);

        return [
            'video' => $videoExists,
            'thumbnail' => $thumbnailExists,
            'all' => $videoExists && ($thumbnailExists || !$this->thumbnail_path),
        ];
    }

    /**
     * Obtener posts relacionados basados en el producto
     */
    public function getRelatedPosts($limit = 4)
    {
        if (!$this->product) {
            return self::with('product', 'staff')->public()->where('id', '!=', $this->id)->latest()->limit($limit)->get();
        }

        // Obtener palabras clave del nombre del producto
        $productWords = collect(explode(' ', $this->product->name))->filter(function ($word) {
            return strlen($word) > 3;
        });

        if ($productWords->isEmpty()) {
            return self::with('product', 'staff')->public()->where('id', '!=', $this->id)->latest()->limit($limit)->get();
        }

        // Buscar posts con productos similares
        return self::with('product', 'staff')
            ->public()
            ->where('id', '!=', $this->id)
            ->whereHas('product', function ($query) use ($productWords) {
                $query->where(function ($q) use ($productWords) {
                    foreach ($productWords as $word) {
                        $q->orWhere('name', 'like', "%{$word}%");
                    }
                });
            })
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener posts del mismo producto
     */
    public function getSameProductPosts($limit = 4)
    {
        return self::with('staff')->public()->where('id', '!=', $this->id)->where('product_id', $this->product_id)->latest()->limit($limit)->get();
    }

    /**
     * Obtener posts del mismo staff
     */
    public function getStaffPosts($limit = 4)
    {
        return self::with('product')->public()->where('id', '!=', $this->id)->where('staff_id', $this->staff_id)->latest()->limit($limit)->get();
    }

    /**
     * Obtener posts más populares
     */
    public function getPopularPosts($limit = 4)
    {
        return self::with('product', 'staff')->public()->where('id', '!=', $this->id)->orderBy('views', 'desc')->limit($limit)->get();
    }

    /**
     * Estrategia inteligente de posts relacionados
     */
    public function getSmartRelatedPosts($limit = 8)
    {
        $related = collect();

        // 1. Mismo producto (máximo 2)
        $sameProduct = $this->getSameProductPosts(2);
        $related = $related->merge($sameProduct);

        // 2. Productos similares (máximo 3)
        if ($related->count() < $limit) {
            $similar = $this->getRelatedPosts($limit - $related->count());
            $related = $related->merge($similar);
        }

        // 3. Mismo staff (máximo 2)
        if ($related->count() < $limit) {
            $staffPosts = $this->getStaffPosts($limit - $related->count());
            $related = $related->merge($staffPosts);
        }

        // 4. Populares (completar hasta el límite)
        if ($related->count() < $limit) {
            $popular = $this->getPopularPosts($limit - $related->count());
            $related = $related->merge($popular);
        }

        return $related->unique('id')->take($limit);
    }
}
