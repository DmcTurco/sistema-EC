<?php

namespace App\Http\Controllers\Staff;

use App\Models\Staff;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Staff::with('store');

        // Búsqueda
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por tienda
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Ordenar por más recientes
        $staff = $query->latest()->paginate(15)->withQueryString();

        // Obtener tiendas para el filtro
        $stores = Store::active()->orderBy('name')->get();

        return view('admin.pages.staff.index', compact('staff', 'stores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $stores = Store::active()->orderBy('name')->get();
        return view('admin.pages.staff.form', compact('stores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:staff,email',
                'password' => 'required|string|min:6|confirmed',
                'store_id' => 'nullable|exists:stores,id',
            ],
            [
                'name.required' => 'El nombre es obligatorio',
                'email.required' => 'El email es obligatorio',
                'email.email' => 'El email debe ser válido',
                'email.unique' => 'Este email ya está registrado',
                'password.required' => 'La contraseña es obligatoria',
                'password.min' => 'La contraseña debe tener al menos 6 caracteres',
                'password.confirmed' => 'Las contraseñas no coinciden',
                'store_id.exists' => 'La tienda seleccionada no es válida',
            ],
        );

        try {
            DB::beginTransaction();

            // Status (checkbox)
            $validated['status'] = $request->has('status') ? Staff::STATUS_ACTIVE : Staff::STATUS_INACTIVE;

            Staff::create($validated);

            DB::commit();

            return redirect()->route('admin.staff.index')->with('success', 'Personal creado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Error al crear el personal: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Staff $staff)
    {
        $staff->load('store', 'posts');
        return view('admin.pages.staff.show', compact('staff'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Staff $staff)
    {
        $stores = Store::active()->orderBy('name')->get();
        return view('admin.pages.staff.form', compact('staff', 'stores'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => ['required', 'email', 'max:255', Rule::unique('staff', 'email')->ignore($staff->id)],
                'password' => 'nullable|string|min:6|confirmed',
                'store_id' => 'nullable|exists:stores,id',
            ],
            [
                'name.required' => 'El nombre es obligatorio',
                'email.required' => 'El email es obligatorio',
                'email.email' => 'El email debe ser válido',
                'email.unique' => 'Este email ya está registrado',
                'password.min' => 'La contraseña debe tener al menos 6 caracteres',
                'password.confirmed' => 'Las contraseñas no coinciden',
                'store_id.exists' => 'La tienda seleccionada no es válida',
            ],
        );

        try {
            DB::beginTransaction();

            // Status (checkbox)
            $validated['status'] = $request->has('status') ? Staff::STATUS_ACTIVE : Staff::STATUS_INACTIVE;

            // Si no se envió password, no actualizar
            if (empty($validated['password'])) {
                unset($validated['password']);
            }

            $staff->update($validated);

            DB::commit();

            return redirect()->route('admin.staff.index')->with('success', 'Personal actualizado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Error al actualizar el personal: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Staff $staff)
    {
        try {
            // Soft Delete
            $staff->delete();

            return redirect()->route('admin.staff.index')->with('success', 'Personal eliminado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el personal: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft deleted staff
     */
    public function restore($id)
    {
        try {
            $staff = Staff::withTrashed()->findOrFail($id);
            $staff->restore();

            return redirect()->route('admin.staff.index')->with('success', 'Personal restaurado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al restaurar el personal: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete a staff
     */
    public function forceDelete($id)
    {
        try {
            $staff = Staff::withTrashed()->findOrFail($id);

            // Verificar si tiene publicaciones
            // if ($staff->posts()->count() > 0) {
            //     return back()->with('error', 'No se puede eliminar. El personal tiene publicaciones asociadas.');
            // }

            $staff->forceDelete();

            return redirect()->route('admin.staff.index')->with('success', 'Personal eliminado permanentemente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar permanentemente el personal: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete staff
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'staff_ids' => 'required|array',
            'staff_ids.*' => 'exists:staff,id',
        ]);

        try {
            Staff::whereIn('id', $request->staff_ids)->delete();

            return redirect()
                ->route('admin.staff.index')
                ->with('success', count($request->staff_ids) . ' miembros del personal eliminados');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar personal: ' . $e->getMessage());
        }
    }

    /**
     * Toggle staff status (AJAX)
     */
    public function toggleStatus(Staff $staff)
    {
        try {
            $newStatus = $staff->status === Staff::STATUS_ACTIVE ? Staff::STATUS_INACTIVE : Staff::STATUS_ACTIVE;

            $staff->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado',
                'status' => $newStatus,
                'status_text' => $staff->status_text,
                'status_badge' => $staff->status_badge,
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
