<?php

namespace App\Http\Controllers\Api;

use App\Models\General;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    /**
     * Registro de nuevo usuario
     */
    public function register(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                'phone' => 'nullable|string|max:20',
                'preferred_store_id' => 'nullable|exists:stores,id',
            ],
            [
                'name.required' => 'El nombre es obligatorio',
                'email.required' => 'El email es obligatorio',
                'email.email' => 'El email debe ser válido',
                'email.unique' => 'Este email ya está registrado',
                'password.required' => 'La contraseña es obligatoria',
                'password.min' => 'La contraseña debe tener al menos 6 caracteres',
                'password.confirmed' => 'Las contraseñas no coinciden',
            ],
        );

        try {
            $user = General::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'preferred_store_id' => $validated['preferred_store_id'] ?? null,
            ]);

            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Usuario registrado exitosamente',
                    'data' => [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'phone' => $user->phone,
                            'preferred_store_id' => $user->preferred_store_id,
                        ],
                        'token' => $token,
                    ],
                ],
                201,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al registrar usuario: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Login de usuario
     */
    public function login(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required',
            ],
            [
                'email.required' => 'El email es obligatorio',
                'email.email' => 'El email debe ser válido',
                'password.required' => 'La contraseña es obligatoria',
            ],
        );

        $user = General::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales son incorrectas.'],
            ]);
        }

        // Crear token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json(
            [
                'success' => true,
                'message' => 'Login exitoso',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'address' => $user->address,
                        'city' => $user->city,
                        'state' => $user->state,
                        'postal_code' => $user->postal_code,
                        'preferred_store_id' => $user->preferred_store_id,
                        'preferred_store' => $user->preferredStore
                            ? [
                                'id' => $user->preferredStore->id,
                                'name' => $user->preferredStore->name,
                            ]
                            : null,
                    ],
                    'token' => $token,
                ],
            ],
            200,
        );
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(
            [
                'success' => true,
                'message' => 'Logout exitoso',
            ],
            200,
        );
    }

    /**
     * Obtener información del usuario autenticado
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json(
            [
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'city' => $user->city,
                    'state' => $user->state,
                    'postal_code' => $user->postal_code,
                    'full_address' => $user->full_address,
                    'has_complete_address' => $user->has_complete_address,
                    'preferred_store_id' => $user->preferred_store_id,
                    'preferred_store' => $user->preferredStore
                        ? [
                            'id' => $user->preferredStore->id,
                            'name' => $user->preferredStore->name,
                            'address' => $user->preferredStore->address,
                        ]
                        : null,
                ],
            ],
            200,
        );
    }

    /**
     * Actualizar perfil del usuario
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate(
            [
                'name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'preferred_store_id' => 'nullable|exists:stores,id',
            ],
            [
                'preferred_store_id.exists' => 'La tienda seleccionada no existe',
            ],
        );

        try {
            $user->update($validated);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Perfil actualizado exitosamente',
                    'data' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'address' => $user->address,
                        'city' => $user->city,
                        'state' => $user->state,
                        'postal_code' => $user->postal_code,
                        'full_address' => $user->full_address,
                        'preferred_store_id' => $user->preferred_store_id,
                    ],
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al actualizar perfil: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Cambiar contraseña
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate(
            [
                'current_password' => 'required',
                'new_password' => 'required|string|min:6|confirmed',
            ],
            [
                'current_password.required' => 'La contraseña actual es obligatoria',
                'new_password.required' => 'La nueva contraseña es obligatoria',
                'new_password.min' => 'La nueva contraseña debe tener al menos 6 caracteres',
                'new_password.confirmed' => 'Las contraseñas no coinciden',
            ],
        );

        // Verificar contraseña actual
        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'La contraseña actual es incorrecta',
                ],
                400,
            );
        }

        try {
            $user->update([
                'password' => Hash::make($validated['new_password']),
            ]);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Contraseña actualizada exitosamente',
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al cambiar contraseña: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Historial de pedidos del usuario
     */
    public function orders(Request $request)
    {
        $user = $request->user();

        $query = $user->orders()->with('product')->latest();

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $orders = $query->paginate($perPage);

        return response()->json(
            [
                'success' => true,
                'data' => [
                    'orders' => $orders->map(function ($order) {
                        return [
                            'id' => $order->id,
                            'order_number' => $order->order_number,
                            'product' => [
                                'id' => $order->product->id,
                                'name' => $order->product->name,
                                'image' => $order->product->image_url,
                            ],
                            'quantity' => $order->quantity,
                            'total_amount' => $order->total_amount,
                            'formatted_total' => $order->formatted_total,
                            'status' => $order->status_text,
                            'created_at' => $order->created_at->format('d/m/Y H:i'),
                        ];
                    }),
                    'pagination' => [
                        'total' => $orders->total(),
                        'per_page' => $orders->perPage(),
                        'current_page' => $orders->currentPage(),
                        'last_page' => $orders->lastPage(),
                    ],
                ],
            ],
            200,
        );
    }

    /**
     * Cupones del usuario
     */
    public function coupons(Request $request)
    {
        $user = $request->user();

        // Filtro: disponibles o usados
        $status = $request->get('status', 'available'); // available | used | all

        if ($status === 'available') {
            $coupons = $user->availableCoupons;
        } elseif ($status === 'used') {
            $coupons = $user->usedCoupons;
        } else {
            $coupons = $user->coupons;
        }

        return response()->json(
            [
                'success' => true,
                'data' => [
                    'coupons' => $coupons->map(function ($coupon) {
                        return [
                            'id' => $coupon->id,
                            'user_coupon_id' => $coupon->pivot->id,
                            'name' => $coupon->name,
                            'description' => $coupon->description,
                            'image' => $coupon->image_url,
                            'min_purchase' => $coupon->min_purchase,
                            'formatted_min_purchase' => $coupon->formatted_min_purchase,
                            'status' => $coupon->pivot->status === 0 ? 'Disponible' : 'Usado',
                            'obtained_at' => $coupon->pivot->created_at->format('d/m/Y'),
                            'used_at' => $coupon->pivot->used_at ? $coupon->pivot->used_at->format('d/m/Y H:i') : null,
                        ];
                    }),
                ],
            ],
            200,
        );
    }

    /**
     * Marcar cupón como usado
     */
    public function markCouponAsUsed(Request $request, $userCouponId)
    {
        $user = $request->user();

        $updated = $user->markCouponAsUsed($userCouponId);

        if (!$updated) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Cupón no encontrado o ya fue usado',
                ],
                404,
            );
        }

        return response()->json(
            [
                'success' => true,
                'message' => 'Cupón marcado como usado',
            ],
            200,
        );
    }

    /**
     * Listar tiendas disponibles
     */
    public function stores(Request $request)
    {
        $query = Store::active();

        // Búsqueda
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $stores = $query->orderBy('name')->get();

        return response()->json(
            [
                'success' => true,
                'data' => [
                    'stores' => $stores->map(function ($store) {
                        return [
                            'id' => $store->id,
                            'name' => $store->name,
                            'address' => $store->address,
                        ];
                    }),
                ],
            ],
            200,
        );
    }
}
