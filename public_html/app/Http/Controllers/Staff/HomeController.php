<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $staff = Auth::guard('staff')->user();

        // Estadísticas
        $stats = [
            'total_posts' => Post::byStaff($staff->id)->count(),
            'public_posts' => Post::byStaff($staff->id)->public()->count(),
            'total_views' => Post::byStaff($staff->id)->sum('views'),
            'total_sales' => Post::byStaff($staff->id)->sum('sales'),
        ];

        // Publicaciones recientes
        $recent_posts = Post::with('product')->byStaff($staff->id)->latest()->limit(5)->get();

        // Publicaciones más vistas
        $popular_posts = Post::with('product')->byStaff($staff->id)->orderBy('views', 'desc')->limit(5)->get();

        return view('staff.pages.home', compact('stats', 'recent_posts', 'popular_posts'));
    }
}
