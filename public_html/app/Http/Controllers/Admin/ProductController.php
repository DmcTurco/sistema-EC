<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Búsqueda
        if ($request->filled('search')) {
            $query->search($request->search); // ✅ Corregido
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por stock
        if ($request->filled('stock_filter')) {
            switch ($request->stock_filter) {
                case 'low':
                    $query->lowStock(10);
                    break;
                case 'out':
                    $query->outOfStock();
                    break;
                case 'in':
                    $query->inStock();
                    break;
            }
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        return view('admin.pages.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.products.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:2000',
                'price' => 'required|numeric|min:0|max:999999.99',
                'stock' => 'required|integer|min:0|max:999999',
                'sku' => 'nullable|string|max:100|unique:products,sku',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5048',
                'main_video_path' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/webm|max:102400', // 100MB
            ],
            [
                'name.required' => 'El nombre del producto es obligatorio',
                'price.required' => 'El precio es obligatorio',
                'price.min' => 'El precio debe ser mayor o igual a 0',
                'stock.required' => 'El stock es obligatorio',
                'sku.unique' => 'El SKU ya existe',
                'image.image' => 'El archivo debe ser una imagen',
                'image.max' => 'La imagen no debe superar 5MB',
                'main_video_path.mimetypes' => 'El formato del video debe ser MP4, MOV, AVI, WMV o WEBM',
                'main_video_path.max' => 'El video no debe superar 100MB',
            ],
        );

        try {
            DB::beginTransaction();

            // Status (checkbox)
            $validated['status'] = $request->has('status') ? Product::STATUS_ACTIVE : Product::STATUS_INACTIVE;

            // Guardar imagen
            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('products/images', 'public');
            }

            // Guardar video con estructura organizada
            if ($request->hasFile('main_video_path')) {
                // Crear ruta organizada por fecha
                $videoPath = 'products/videos/' . date('Y/m');

                // Crear directorio si no existe
                if (!Storage::disk('public')->exists($videoPath)) {
                    Storage::disk('public')->makeDirectory($videoPath);
                }

                // Nombre descriptivo con slug del producto
                $videoFile = $request->file('main_video_path');
                $videoFileName = Str::slug($validated['name']) . '-' . time() . '.' . $videoFile->getClientOriginalExtension();

                // Guardar video
                $validated['main_video_path'] = $videoFile->storeAs($videoPath, $videoFileName, 'public');

                // Generar thumbnail automáticamente
                try {
                    $thumbnailPath = $videoPath . '/thumbnails';
                    if (!Storage::disk('public')->exists($thumbnailPath)) {
                        Storage::disk('public')->makeDirectory($thumbnailPath);
                    }

                    $ffmpeg = FFMpeg::create([
                        'ffmpeg.binaries' => PHP_OS_FAMILY === 'Windows' ? base_path('bin/ffmpeg/ffmpeg.exe') : '/usr/bin/ffmpeg',
                        'ffprobe.binaries' => PHP_OS_FAMILY === 'Windows' ? base_path('bin/ffmpeg/ffprobe.exe') : '/usr/bin/ffprobe',
                        'timeout' => 3600,
                        'ffmpeg.threads' => 12,
                    ]);

                    $videoStoragePath = Storage::disk('public')->path($validated['main_video_path']);
                    $thumbnailFileName = pathinfo($videoFileName, PATHINFO_FILENAME) . '.jpg';
                    $thumbnailStoragePath = Storage::disk('public')->path($thumbnailPath . '/' . $thumbnailFileName);

                    $video = $ffmpeg->open($videoStoragePath);
                    $frame = $video->frame(TimeCode::fromSeconds(3));
                    $frame->save($thumbnailStoragePath);

                    $validated['video_thumbnail'] = $thumbnailPath . '/' . $thumbnailFileName;

                    Log::info('Thumbnail generado correctamente: ' . $thumbnailStoragePath);
                } catch (\Exception $e) {
                    Log::error('Error al generar thumbnail: ' . $e->getMessage());
                    $validated['video_thumbnail'] = null;
                }
            }

            Product::create($validated);

            DB::commit();

            return redirect()->route('admin.products.index')->with('success', 'Producto creado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();

            // Eliminar archivos si hubo error
            if (isset($validated['image'])) {
                Storage::disk('public')->delete($validated['image']);
            }
            if (isset($validated['main_video_path'])) {
                Storage::disk('public')->delete($validated['main_video_path']);
            }
            if (isset($validated['video_thumbnail'])) {
                Storage::disk('public')->delete($validated['video_thumbnail']);
            }

            Log::error('Error al crear el producto: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Error al crear el producto: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('admin.pages.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('admin.pages.products.form', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:2000',
                'price' => 'required|numeric|min:0|max:999999.99',
                'stock' => 'required|integer|min:0|max:999999',
                'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5048',
                'main_video_path' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/webm|max:102400',
            ],
            [
                'name.required' => 'El nombre del producto es obligatorio',
                'price.required' => 'El precio es obligatorio',
                'price.min' => 'El precio debe ser mayor o igual a 0',
                'stock.required' => 'El stock es obligatorio',
                'sku.unique' => 'El SKU ya existe',
                'image.image' => 'El archivo debe ser una imagen',
                'image.max' => 'La imagen no debe superar 5MB',
                'main_video_path.mimetypes' => 'El formato del video debe ser MP4, MOV, AVI, WMV o WEBM',
                'main_video_path.max' => 'El video no debe superar 100MB',
            ],
        );

        try {
            DB::beginTransaction();

            // Status (checkbox)
            $validated['status'] = $request->has('status') ? Product::STATUS_ACTIVE : Product::STATUS_INACTIVE;

            $oldImage = $product->image;
            $oldVideo = $product->main_video_path;
            $oldThumbnail = $product->video_thumbnail;

            // Eliminar imagen si se marcó
            if ($request->boolean('remove_image') && $product->image) {
                Storage::disk('public')->delete($product->image);
                $validated['image'] = null;
            }

            // Nueva imagen
            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $validated['image'] = $request->file('image')->store('products/images', 'public');
            }

            // Eliminar video y thumbnail si se marcó
            if ($request->boolean('remove_video') && $product->main_video_path) {
                Storage::disk('public')->delete($product->main_video_path);
                if ($product->video_thumbnail) {
                    Storage::disk('public')->delete($product->video_thumbnail);
                }
                $validated['main_video_path'] = null;
                $validated['video_thumbnail'] = null;
            }

            // Nuevo video
            if ($request->hasFile('main_video_path')) {
                // Eliminar video y thumbnail anteriores
                if ($product->main_video_path) {
                    Storage::disk('public')->delete($product->main_video_path);
                }
                if ($product->video_thumbnail) {
                    Storage::disk('public')->delete($product->video_thumbnail);
                }

                // Crear ruta organizada por fecha
                $videoPath = 'products/videos/' . date('Y/m');

                if (!Storage::disk('public')->exists($videoPath)) {
                    Storage::disk('public')->makeDirectory($videoPath);
                }

                $videoFile = $request->file('main_video_path');
                $videoFileName = Str::slug($validated['name']) . '-' . time() . '.' . $videoFile->getClientOriginalExtension();

                $validated['main_video_path'] = $videoFile->storeAs($videoPath, $videoFileName, 'public');

                // Generar thumbnail
                try {
                    $thumbnailPath = $videoPath . '/thumbnails';
                    if (!Storage::disk('public')->exists($thumbnailPath)) {
                        Storage::disk('public')->makeDirectory($thumbnailPath);
                    }

                    $ffmpeg = FFMpeg::create([
                        'ffmpeg.binaries' => PHP_OS_FAMILY === 'Windows' ? base_path('bin/ffmpeg/ffmpeg.exe') : '/usr/bin/ffmpeg',
                        'ffprobe.binaries' => PHP_OS_FAMILY === 'Windows' ? base_path('bin/ffmpeg/ffprobe.exe') : '/usr/bin/ffprobe',
                        'timeout' => 3600,
                        'ffmpeg.threads' => 12,
                    ]);

                    $videoStoragePath = Storage::disk('public')->path($validated['main_video_path']);
                    $thumbnailFileName = pathinfo($videoFileName, PATHINFO_FILENAME) . '.jpg';
                    $thumbnailStoragePath = Storage::disk('public')->path($thumbnailPath . '/' . $thumbnailFileName);

                    $video = $ffmpeg->open($videoStoragePath);
                    $frame = $video->frame(TimeCode::fromSeconds(3));
                    $frame->save($thumbnailStoragePath);

                    $validated['video_thumbnail'] = $thumbnailPath . '/' . $thumbnailFileName;

                    Log::info('Thumbnail generado correctamente: ' . $thumbnailStoragePath);
                } catch (\Exception $e) {
                    Log::error('Error al generar thumbnail: ' . $e->getMessage());
                    $validated['video_thumbnail'] = null;
                }
            }

            $product->update($validated);

            DB::commit();

            return redirect()->route('admin.products.index')->with('success', 'Producto actualizado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();

            // Restaurar archivos si hubo error
            if (isset($validated['image']) && $validated['image'] !== $oldImage) {
                Storage::disk('public')->delete($validated['image']);
            }
            if (isset($validated['main_video_path']) && $validated['main_video_path'] !== $oldVideo) {
                Storage::disk('public')->delete($validated['main_video_path']);
            }
            if (isset($validated['video_thumbnail']) && $validated['video_thumbnail'] !== $oldThumbnail) {
                Storage::disk('public')->delete($validated['video_thumbnail']);
            }

            Log::error('Error al actualizar el producto: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Error al actualizar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            // Soft Delete (ya que tu modelo usa SoftDeletes)
            $product->delete();

            return redirect()->route('admin.products.index')->with('success', 'Producto eliminado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft deleted product
     */
    public function restore($id)
    {
        try {
            $product = Product::withTrashed()->findOrFail($id);
            $product->restore();

            return redirect()->route('admin.products.index')->with('success', 'Producto restaurado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al restaurar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete a product
     */
    public function forceDelete($id)
    {
        try {
            $product = Product::withTrashed()->findOrFail($id);

            // Eliminar archivos físicos
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            if ($product->main_video_path) {
                Storage::disk('public')->delete($product->main_video_path);
            }
            if ($product->video_thumbnail) {
                // ✅ AGREGAR ESTO
                Storage::disk('public')->delete($product->video_thumbnail);
            }

            $product->forceDelete();

            return redirect()->route('admin.products.index')->with('success', 'Producto eliminado permanentemente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar permanentemente el producto: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete products
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        try {
            Product::whereIn('id', $request->product_ids)->delete();

            return redirect()
                ->route('admin.products.index')
                ->with('success', count($request->product_ids) . ' productos eliminados');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar productos: ' . $e->getMessage());
        }
    }

    /**
     * Update stock
     */
    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        try {
            $product->update(['stock' => $request->stock]);

            return response()->json([
                'success' => true,
                'message' => 'Stock actualizado',
                'stock' => $product->stock,
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al actualizar stock',
                ],
                500,
            );
        }
    }
}
