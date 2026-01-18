<?php

namespace App\Http\Controllers\Admin;

use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Store::query();

        // Búsqueda
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Ordenar por más recientes
        $stores = $query->latest()->paginate(15)->withQueryString();

        return view('admin.pages.stores.index', compact('stores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.stores.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
        ], [
            'name.required' => 'El nombre de la tienda es obligatorio',
            'name.max' => 'El nombre no puede exceder 255 caracteres',
            'address.max' => 'La dirección no puede exceder 500 caracteres',
        ]);

        try {
            DB::beginTransaction();

            // Status (checkbox)
            $validated['status'] = $request->has('status') 
                ? Store::STATUS_ACTIVE 
                : Store::STATUS_INACTIVE;

            Store::create($validated);

            DB::commit();

            return redirect()
                ->route('admin.stores.index')
                ->with('success', 'Tienda creada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Error al crear la tienda: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store)
    {
        return view('admin.pages.stores.show', compact('store'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Store $store)
    {
        return view('admin.pages.stores.form', compact('store'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Store $store)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
        ], [
            'name.required' => 'El nombre de la tienda es obligatorio',
            'name.max' => 'El nombre no puede exceder 255 caracteres',
            'address.max' => 'La dirección no puede exceder 500 caracteres',
        ]);

        try {
            DB::beginTransaction();

            // Status (checkbox)
            $validated['status'] = $request->has('status') 
                ? Store::STATUS_ACTIVE 
                : Store::STATUS_INACTIVE;

            $store->update($validated);

            DB::commit();

            return redirect()
                ->route('admin.stores.index')
                ->with('success', 'Tienda actualizada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Error al actualizar la tienda: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store)
    {
        try {
            // Soft Delete
            $store->delete();

            return redirect()
                ->route('admin.stores.index')
                ->with('success', 'Tienda eliminada exitosamente');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error al eliminar la tienda: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft deleted store
     */
    public function restore($id)
    {
        try {
            $store = Store::withTrashed()->findOrFail($id);
            $store->restore();

            return redirect()
                ->route('admin.stores.index')
                ->with('success', 'Tienda restaurada exitosamente');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error al restaurar la tienda: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete a store
     */
    public function forceDelete($id)
    {
        try {
            $store = Store::withTrashed()->findOrFail($id);

            // Verificar si tiene relaciones (descomentar según necesites)
            // if ($store->users()->count() > 0) {
            //     return back()->with('error', 'No se puede eliminar. La tienda tiene usuarios asociados.');
            // }
            // if ($store->posts()->count() > 0) {
            //     return back()->with('error', 'No se puede eliminar. La tienda tiene publicaciones asociadas.');
            // }

            $store->forceDelete();

            return redirect()
                ->route('admin.stores.index')
                ->with('success', 'Tienda eliminada permanentemente');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error al eliminar permanentemente la tienda: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete stores
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'store_ids' => 'required|array',
            'store_ids.*' => 'exists:stores,id',
        ]);

        try {
            Store::whereIn('id', $request->store_ids)->delete();

            return redirect()
                ->route('admin.stores.index')
                ->with('success', count($request->store_ids) . ' tiendas eliminadas');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error al eliminar tiendas: ' . $e->getMessage());
        }
    }

    /**
     * Toggle store status (AJAX)
     */
    public function toggleStatus(Store $store)
    {
        try {
            $newStatus = $store->status === Store::STATUS_ACTIVE 
                ? Store::STATUS_INACTIVE 
                : Store::STATUS_ACTIVE;

            $store->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado',
                'status' => $newStatus,
                'status_text' => $store->status_text,
                'status_badge' => $store->status_badge,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar estado',
            ], 500);
        }
    }

    /**
     * Get stores for select dropdown (AJAX)
     */
    public function getStoresForSelect(Request $request)
    {
        $query = Store::active();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $stores = $query->select('id', 'name', 'address')
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->map(function ($store) {
                return [
                    'id' => $store->id,
                    'text' => $store->name . ($store->address ? ' - ' . $store->address : ''),
                ];
            });

        return response()->json($stores);
    }

    /**
     * Show trashed stores
     */
    public function trashed()
    {
        $stores = Store::onlyTrashed()
            ->latest('deleted_at')
            ->paginate(15);

        return view('admin.pages.stores.trashed', compact('stores'));
    }

    /**
     * Restore multiple stores
     */
    public function bulkRestore(Request $request)
    {
        $request->validate([
            'store_ids' => 'required|array',
            'store_ids.*' => 'exists:stores,id',
        ]);

        try {
            Store::withTrashed()
                ->whereIn('id', $request->store_ids)
                ->restore();

            return redirect()
                ->route('admin.stores.index')
                ->with('success', count($request->store_ids) . ' tiendas restauradas');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error al restaurar tiendas: ' . $e->getMessage());
        }
    }
}