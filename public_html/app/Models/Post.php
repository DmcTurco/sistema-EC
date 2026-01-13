<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $table = 'posts';

    protected $fillable = [
        'staff_id',
        'product_id',
        'intro_video_path',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];
}
