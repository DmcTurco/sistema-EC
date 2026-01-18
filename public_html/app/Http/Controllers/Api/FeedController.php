<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FeedController extends Controller
{
    /**
     * Obtener feed de posts públicos para la app móvil
     */
    public function index(Request $request)
    {
        $query = Post::with(['product', 'staff.store'])
            ->public()
            ->latest();

        // Filtro por producto
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filtro por tienda
        if ($request->filled('store_id')) {
            $query->whereHas('staff', function($q) use ($request) {
                $q->where('store_id', $request->store_id);
            });
        }

        // Búsqueda por nombre de producto
        if ($request->filled('search')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            });
        }

        // Paginación
        $perPage = $request->get('per_page', 15);
        $posts = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'posts' => $posts->map(function ($post) {
                    return [
                        'id' => $post->id,
                        'product' => [
                            'id' => $post->product->id,
                            'name' => $post->product->name,
                            'description' => $post->product->description,
                            'price' => $post->product->price,
                            'formatted_price' => $post->product->formatted_price,
                            'image' => $post->product->image_url,
                            'main_video' => $post->product->main_video_url,
                            'stock' => $post->product->stock,
                        ],
                        'staff' => [
                            'id' => $post->staff->id,
                            'name' => $post->staff->name,
                            'store' => $post->staff->store ? $post->staff->store->name : null,
                        ],
                        'intro_video' => $post->intro_video_url,
                        'thumbnail' => $post->thumbnail_url,
                        'views' => $post->views,
                        'sales' => $post->sales,
                        'created_at' => $post->created_at->toIso8601String(),
                    ];
                }),
                'pagination' => [
                    'total' => $posts->total(),
                    'per_page' => $posts->perPage(),
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'from' => $posts->firstItem(),
                    'to' => $posts->lastItem(),
                ],
            ],
        ], 200);
    }

    /**
     * Obtener un post específico
     */
    public function show($id)
    {
        $post = Post::with(['product', 'staff.store'])
            ->public()
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $post->id,
                'product' => [
                    'id' => $post->product->id,
                    'name' => $post->product->name,
                    'description' => $post->product->description,
                    'price' => $post->product->price,
                    'formatted_price' => $post->product->formatted_price,
                    'image' => $post->product->image_url,
                    'main_video' => $post->product->main_video_url,
                    'stock' => $post->product->stock,
                ],
                'staff' => [
                    'id' => $post->staff->id,
                    'name' => $post->staff->name,
                    'store' => $post->staff->store ? $post->staff->store->name : null,
                ],
                'intro_video' => $post->intro_video_url,
                'thumbnail' => $post->thumbnail_url,
                'views' => $post->views,
                'sales' => $post->sales,
                'created_at' => $post->created_at->toIso8601String(),
            ],
        ], 200);
    }

    /**
     * Listar productos disponibles
     */
    public function products(Request $request)
    {
        $query = Product::active();

        // Búsqueda
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        // Filtro por tienda
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Ordenar por nombre
        $query->orderBy('name');

        $perPage = $request->get('per_page', 20);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'description' => $product->description,
                        'price' => $product->price,
                        'formatted_price' => $product->formatted_price,
                        'stock' => $product->stock,
                        'image' => $product->image_url,
                        'main_video' => $product->main_video_url,
                    ];
                }),
                'pagination' => [
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                ],
            ],
        ], 200);
    }
}