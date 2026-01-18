<?php

namespace App\Http\Controllers\Staff;

use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $staff = Auth::guard('staff')->user();
        $query = Post::with('product')->byStaff($staff->id);

        // Búsqueda
        if ($request->filled('search')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            });
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $posts = $query->latest()->paginate(12)->withQueryString();

        return view('staff.pages.post.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     $staff = Auth::guard('staff')->user();

    //     // Solo productos de su tienda
    //     $products = Product::where('store_id', $staff->store_id)->active()->orderBy('name')->get();

    //     return view('staff.posts.form', compact('products'));
    // }
    public function create()
    {
        $staff = Auth::guard('staff')->user();

        // Todos los productos activos
        $products = Product::active()->orderBy('name')->get();

        return view('staff.pages.post.form', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $staff = Auth::guard('staff')->user();

        $validated = $request->validate(
            [
                'product_id' => 'required|exists:products,id',
                'intro_video' => 'required|file|mimes:mp4,mov,avi,webm|max:10240', // 10MB max
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ],
            [
                'product_id.required' => 'Debes seleccionar un producto',
                'product_id.exists' => 'El producto seleccionado no es válido',
                'intro_video.required' => 'El video de saludo es obligatorio',
                'intro_video.mimes' => 'El video debe ser MP4, MOV, AVI o WEBM',
                'intro_video.max' => 'El video no debe superar 10MB',
                'thumbnail.image' => 'La miniatura debe ser una imagen',
                'thumbnail.max' => 'La miniatura no debe superar 2MB',
            ],
        );

        try {
            DB::beginTransaction();

            // Subir video
            $videoPath = $request->file('intro_video')->store('posts/videos', 'public');

            // Subir thumbnail si existe
            $thumbnailPath = null;
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('posts/thumbnails', 'public');
            }

            // Crear publicación
            Post::create([
                'staff_id' => $staff->id,
                'product_id' => $validated['product_id'],
                'intro_video_path' => $videoPath,
                'thumbnail_path' => $thumbnailPath,
                'status' => $request->has('status') ? Post::STATUS_PUBLIC : Post::STATUS_PRIVATE,
            ]);

            DB::commit();

            return redirect()->route('staff.posts.index')->with('success', 'Publicación creada exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();

            // Limpiar archivos si falla
            if (isset($videoPath)) {
                Storage::disk('public')->delete($videoPath);
            }
            if (isset($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }

            return back()
                ->withInput()
                ->with('error', 'Error al crear la publicación: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $staff = Auth::guard('staff')->user();

        // Verificar que la publicación pertenece al staff
        if ($post->staff_id !== $staff->id) {
            abort(403, 'No tienes permiso para editar esta publicación');
        }

        $products = Product::where('store_id', $staff->store_id)->active()->orderBy('name')->get();

        return view('staff.posts.form', compact('post', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $staff = Auth::guard('staff')->user();

        // Verificar permisos
        if ($post->staff_id !== $staff->id) {
            abort(403);
        }

        $validated = $request->validate(
            [
                'product_id' => 'required|exists:products,id',
                'intro_video' => 'nullable|file|mimes:mp4,mov,avi,webm|max:10240',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ],
            [
                'product_id.required' => 'Debes seleccionar un producto',
                'intro_video.mimes' => 'El video debe ser MP4, MOV, AVI o WEBM',
                'intro_video.max' => 'El video no debe superar 10MB',
                'thumbnail.max' => 'La miniatura no debe superar 2MB',
            ],
        );

        try {
            DB::beginTransaction();

            $data = [
                'product_id' => $validated['product_id'],
                'status' => $request->has('status') ? Post::STATUS_PUBLIC : Post::STATUS_PRIVATE,
            ];

            // Actualizar video si se subió uno nuevo
            if ($request->hasFile('intro_video')) {
                // Eliminar video anterior
                if ($post->intro_video_path) {
                    Storage::disk('public')->delete($post->intro_video_path);
                }
                $data['intro_video_path'] = $request->file('intro_video')->store('posts/videos', 'public');
            }

            // Actualizar thumbnail si se subió uno nuevo
            if ($request->hasFile('thumbnail')) {
                if ($post->thumbnail_path) {
                    Storage::disk('public')->delete($post->thumbnail_path);
                }
                $data['thumbnail_path'] = $request->file('thumbnail')->store('posts/thumbnails', 'public');
            }

            // Eliminar thumbnail si se solicitó
            if ($request->boolean('remove_thumbnail') && $post->thumbnail_path) {
                Storage::disk('public')->delete($post->thumbnail_path);
                $data['thumbnail_path'] = null;
            }

            $post->update($data);

            DB::commit();

            return redirect()->route('staff.posts.index')->with('success', 'Publicación actualizada exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $staff = Auth::guard('staff')->user();

        // Verificar permisos
        if ($post->staff_id !== $staff->id) {
            abort(403);
        }

        try {
            // Soft delete (no eliminar archivos)
            $post->delete();

            return redirect()->route('staff.posts.index')->with('success', 'Publicación eliminada exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado de publicación (AJAX)
     */
    public function toggleStatus(Post $post)
    {
        $staff = Auth::guard('staff')->user();

        if ($post->staff_id !== $staff->id) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        try {
            $post->toggleStatus();

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado',
                'status' => $post->status,
                'status_text' => $post->status_text,
                'status_badge' => $post->status_badge,
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al actualizar estado',
                ],
                500,
            );
        }
    }
}
