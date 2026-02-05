<?php

namespace App\Http\Controllers\Staff;

use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;

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
        Log::info("message",['posts',$posts]);

        return view('staff.pages.post.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
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
        Log::info('Iniciando creación de post para staff ID: ' . $staff->id);

        $validated = $request->validate(
            [
                'product_id' => 'required|exists:products,id',
                'intro_video' => 'required|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/webm|max:102400', // 100MB
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ],
            [
                'product_id.required' => 'Debes seleccionar un producto',
                'product_id.exists' => 'El producto seleccionado no es válido',
                'intro_video.required' => 'El video de saludo es obligatorio',
                'intro_video.mimetypes' => 'El video debe ser MP4, MOV, AVI o WEBM',
                'intro_video.max' => 'El video no debe superar 100MB',
                'thumbnail.image' => 'La miniatura debe ser una imagen',
                'thumbnail.max' => 'La miniatura no debe superar 2MB',
            ],
        );

        $videoPath = null;
        $thumbnailPath = null;

        try {
            DB::beginTransaction();

            // ✅ Obtener producto para nombre descriptivo
            $product = Product::find($validated['product_id']);
            $productSlug = Str::slug($product->name);
            $timestamp = time();

            // ✅ Crear directorio organizado por fecha
            $dateFolder = date('Y/m');
            $videoDirectory = "posts/videos/{$dateFolder}";

            // ✅ Guardar video con nombre descriptivo
            $videoFile = $request->file('intro_video');
            $videoExtension = $videoFile->getClientOriginalExtension();
            $videoFileName = "{$productSlug}-saludo-{$timestamp}.{$videoExtension}";

            $videoPath = $videoFile->storeAs($videoDirectory, $videoFileName, 'public');

            // ✅ Generar thumbnail automático con FFmpeg
            if ($request->hasFile('thumbnail')) {
                // Usuario subió thumbnail manual
                $thumbnailPath = $request->file('thumbnail')->storeAs("{$videoDirectory}/thumbnails", "{$productSlug}-saludo-{$timestamp}.jpg", 'public');
            } else {
                // Generar thumbnail automático
                try {
                    $thumbnailPath = $this->generateVideoThumbnail($videoPath, $videoDirectory, "{$productSlug}-saludo-{$timestamp}");
                } catch (\Exception $e) {
                    Log::warning('No se pudo generar thumbnail automático para post', [
                        'error' => $e->getMessage(),
                        'video_path' => $videoPath,
                    ]);
                    // Continuar sin thumbnail - no es crítico
                }
            }

            // ✅ Crear publicación
            Post::create([
                'staff_id' => $staff->id,
                'product_id' => $validated['product_id'],
                'intro_video_path' => $videoPath,
                'thumbnail_path' => $thumbnailPath,
                'status' => $request->has('status') ? Post::STATUS_PUBLIC : Post::STATUS_PRIVATE,
            ]);

            DB::commit();

            return redirect()->route('staff.post.index')->with('success', 'Publicación creada exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();

            // Limpiar archivos si falla
            if ($videoPath && Storage::disk('public')->exists($videoPath)) {
                Storage::disk('public')->delete($videoPath);
            }
            if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }

            Log::error('Error al crear post', [
                'error' => $e->getMessage(),
                'staff_id' => $staff->id,
            ]);

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

        $products = Product::active()->orderBy('name')->get();

        return view('staff.pages.post.form', compact('post', 'products'));
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
                'intro_video' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/webm|max:102400',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ],
            [
                'product_id.required' => 'Debes seleccionar un producto',
                'intro_video.mimetypes' => 'El video debe ser MP4, MOV, AVI o WEBM',
                'intro_video.max' => 'El video no debe superar 100MB',
                'thumbnail.max' => 'La miniatura no debe superar 2MB',
            ],
        );

        $newVideoPath = null;
        $newThumbnailPath = null;

        try {
            DB::beginTransaction();

            $data = [
                'product_id' => $validated['product_id'],
                'status' => $request->has('status') ? Post::STATUS_PUBLIC : Post::STATUS_PRIVATE,
            ];

            // ✅ Actualizar video si se subió uno nuevo
            if ($request->hasFile('intro_video')) {
                $product = Product::find($validated['product_id']);
                $productSlug = Str::slug($product->name);
                $timestamp = time();
                $dateFolder = date('Y/m');
                $videoDirectory = "posts/videos/{$dateFolder}";

                $videoFile = $request->file('intro_video');
                $videoExtension = $videoFile->getClientOriginalExtension();
                $videoFileName = "{$productSlug}-saludo-{$timestamp}.{$videoExtension}";

                $newVideoPath = $videoFile->storeAs($videoDirectory, $videoFileName, 'public');
                $data['intro_video_path'] = $newVideoPath;

                // ✅ Generar nuevo thumbnail automático
                try {
                    $newThumbnailPath = $this->generateVideoThumbnail($newVideoPath, $videoDirectory, "{$productSlug}-saludo-{$timestamp}");
                    $data['thumbnail_path'] = $newThumbnailPath;
                } catch (\Exception $e) {
                    Log::warning('No se pudo generar thumbnail al actualizar post', [
                        'error' => $e->getMessage(),
                        'post_id' => $post->id,
                    ]);
                }

                // Eliminar video y thumbnail anteriores
                if ($post->intro_video_path && Storage::disk('public')->exists($post->intro_video_path)) {
                    Storage::disk('public')->delete($post->intro_video_path);
                }
                if ($post->thumbnail_path && Storage::disk('public')->exists($post->thumbnail_path)) {
                    Storage::disk('public')->delete($post->thumbnail_path);
                }
            }

            // ✅ Actualizar thumbnail manual si se subió
            if ($request->hasFile('thumbnail')) {
                $product = Product::find($validated['product_id']);
                $productSlug = Str::slug($product->name);
                $timestamp = time();
                $dateFolder = date('Y/m');

                if ($newThumbnailPath && Storage::disk('public')->exists($newThumbnailPath)) {
                    Storage::disk('public')->delete($newThumbnailPath);
                } elseif ($post->thumbnail_path && Storage::disk('public')->exists($post->thumbnail_path)) {
                    Storage::disk('public')->delete($post->thumbnail_path);
                }

                $data['thumbnail_path'] = $request->file('thumbnail')->storeAs("posts/videos/{$dateFolder}/thumbnails", "{$productSlug}-saludo-{$timestamp}.jpg", 'public');
            }

            // ✅ Eliminar thumbnail si se solicitó
            if ($request->boolean('remove_thumbnail') && $post->thumbnail_path) {
                if (Storage::disk('public')->exists($post->thumbnail_path)) {
                    Storage::disk('public')->delete($post->thumbnail_path);
                }
                $data['thumbnail_path'] = null;
            }

            $post->update($data);

            DB::commit();

            return redirect()->route('staff.post.index')->with('success', 'Publicación actualizada exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();

            // Limpiar archivos nuevos si falla
            if ($newVideoPath && Storage::disk('public')->exists($newVideoPath)) {
                Storage::disk('public')->delete($newVideoPath);
            }
            if ($newThumbnailPath && Storage::disk('public')->exists($newThumbnailPath)) {
                Storage::disk('public')->delete($newThumbnailPath);
            }

            Log::error('Error al actualizar post', [
                'error' => $e->getMessage(),
                'post_id' => $post->id,
            ]);

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
     * Forzar eliminación completa (hard delete) con archivos
     */
    public function forceDelete($id)
    {
        $staff = Auth::guard('staff')->user();
        $post = Post::withTrashed()->findOrFail($id);

        if ($post->staff_id !== $staff->id) {
            abort(403);
        }

        try {
            // Eliminar archivos físicos
            if ($post->intro_video_path && Storage::disk('public')->exists($post->intro_video_path)) {
                Storage::disk('public')->delete($post->intro_video_path);
            }
            if ($post->thumbnail_path && Storage::disk('public')->exists($post->thumbnail_path)) {
                Storage::disk('public')->delete($post->thumbnail_path);
            }

            // Eliminar definitivamente
            $post->forceDelete();

            return redirect()->route('staff.posts.index')->with('success', 'Publicación eliminada permanentemente');
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

    /**
     * ✅ Generar thumbnail del video usando FFmpeg
     */
    private function generateVideoThumbnail($videoPath, $directory, $baseFileName)
    {
        $fullVideoPath = storage_path('app/public/' . $videoPath);
        $thumbnailFileName = $baseFileName . '.jpg';
        $thumbnailDirectory = storage_path('app/public/' . $directory . '/thumbnails');
        $thumbnailFullPath = $thumbnailDirectory . '/' . $thumbnailFileName;

        // Crear directorio si no existe
        if (!file_exists($thumbnailDirectory)) {
            mkdir($thumbnailDirectory, 0755, true);
        }

        // Configurar FFmpeg
        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => PHP_OS_FAMILY === 'Windows' ? base_path('bin/ffmpeg/ffmpeg.exe') : '/usr/bin/ffmpeg',
            'ffprobe.binaries' => PHP_OS_FAMILY === 'Windows' ? base_path('bin/ffmpeg/ffprobe.exe') : '/usr/bin/ffprobe',
            'timeout' => 3600,
            'ffmpeg.threads' => 12,
        ]);

        // Abrir video y generar thumbnail en el segundo 2
        $video = $ffmpeg->open($fullVideoPath);
        $frame = $video->frame(TimeCode::fromSeconds(2));
        $frame->save($thumbnailFullPath);

        // Retornar path relativo
        return $directory . '/thumbnails/' . $thumbnailFileName;
    }
}
