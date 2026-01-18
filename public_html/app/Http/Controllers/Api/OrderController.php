<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Post;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Crear nueva orden con múltiples productos
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'referral_post_id' => 'nullable|exists:posts,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'shipping_address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'customer_name.required' => 'El nombre del cliente es obligatorio',
            'items.required' => 'Debes agregar al menos un producto',
            'items.min' => 'Debes agregar al menos un producto',
            'items.*.product_id.required' => 'El ID del producto es obligatorio',
            'items.*.product_id.exists' => 'Uno o más productos no existen',
            'items.*.quantity.required' => 'La cantidad es obligatoria',
            'items.*.quantity.min' => 'La cantidad mínima es 1',
        ]);

        try {
            DB::beginTransaction();

            // Verificar stock y calcular total
            $totalAmount = 0;
            $productsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Verificar stock
                if ($product->stock < $item['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stock insuficiente para {$product->name}. Disponible: {$product->stock}",
                    ], 400);
                }

                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                $productsData[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ];
            }

            // Crear orden
            $order = Order::create([
                'general_id' => auth('sanctum')->id(), // Usuario autenticado o null
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'shipping_address' => $validated['shipping_address'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'total_amount' => $totalAmount,
                'status' => Order::STATUS_PENDING,
                'referral_post_id' => $validated['referral_post_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Crear items de la orden
            foreach ($productsData as $data) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $data['product']->id,
                    'quantity' => $data['quantity'],
                    'price' => $data['price'],
                    'subtotal' => $data['subtotal'],
                ]);

                // Reducir stock
                $data['product']->decrement('stock', $data['quantity']);
            }

            // Si la orden viene de un post, incrementar ventas
            if ($order->referral_post_id) {
                $post = Post::find($order->referral_post_id);
                if ($post) {
                    $post->recordSale();
                }
            }

            // Otorgar cupones si el usuario está autenticado
            $grantedCoupons = [];
            if (auth('sanctum')->check()) {
                $user = auth('sanctum')->user();
                
                // Buscar cupones activos que apliquen
                $eligibleCoupons = Coupon::active()
                    ->where('min_purchase', '<=', $totalAmount)
                    ->get();

                foreach ($eligibleCoupons as $coupon) {
                    $user->grantCoupon($coupon->id, $order->id);
                    $grantedCoupons[] = [
                        'id' => $coupon->id,
                        'name' => $coupon->name,
                        'image' => $coupon->image_url,
                    ];
                }
            }

            DB::commit();

            // Cargar relaciones para la respuesta
            $order->load('items.product');

            return response()->json([
                'success' => true,
                'message' => 'Orden creada exitosamente',
                'data' => [
                    'order' => [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'items' => $order->items->map(function($item) {
                            return [
                                'product_id' => $item->product_id,
                                'product_name' => $item->product->name,
                                'quantity' => $item->quantity,
                                'price' => $item->price,
                                'formatted_price' => $item->formatted_price,
                                'subtotal' => $item->subtotal,
                                'formatted_subtotal' => $item->formatted_subtotal,
                            ];
                        }),
                        'total_amount' => $order->total_amount,
                        'formatted_total' => $order->formatted_total,
                        'status' => $order->status_text,
                        'created_at' => $order->created_at->toIso8601String(),
                    ],
                    'granted_coupons' => $grantedCoupons,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al crear la orden: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Listar órdenes (para admin)
     */
    public function index(Request $request)
    {
        $query = Order::with(['items.product', 'general'])->latest();

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por usuario
        if ($request->filled('general_id')) {
            $query->where('general_id', $request->general_id);
        }

        $perPage = $request->get('per_page', 15);
        $orders = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'orders' => $orders->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'customer_name' => $order->customer_name,
                        'customer_email' => $order->customer_email,
                        'total_items' => $order->getTotalItems(),
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
        ], 200);
    }

    /**
     * Ver detalle de una orden
     */
    public function show($id)
    {
        $order = Order::with(['items.product', 'general', 'referralPost.staff'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer' => [
                    'name' => $order->customer_name,
                    'email' => $order->customer_email,
                    'phone' => $order->customer_phone,
                ],
                'shipping' => [
                    'address' => $order->shipping_address,
                    'city' => $order->city,
                    'state' => $order->state,
                    'postal_code' => $order->postal_code,
                    'full_address' => $order->full_shipping_address,
                ],
                'items' => $order->items->map(function($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'product_image' => $item->product->image_url,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'formatted_price' => $item->formatted_price,
                        'subtotal' => $item->subtotal,
                        'formatted_subtotal' => $item->formatted_subtotal,
                    ];
                }),
                'total_items' => $order->getTotalItems(),
                'total_amount' => $order->total_amount,
                'formatted_total' => $order->formatted_total,
                'status' => $order->status_text,
                'referral_post' => $order->referralPost ? [
                    'id' => $order->referralPost->id,
                    'staff_name' => $order->referralPost->staff->name,
                ] : null,
                'notes' => $order->notes,
                'created_at' => $order->created_at->toIso8601String(),
                'updated_at' => $order->updated_at->toIso8601String(),
            ],
        ], 200);
    }

    /**
     * Ver orden por número (para usuarios)
     */
    public function showByNumber($orderNumber)
    {
        $order = Order::with(['items.product'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'items' => $order->items->map(function($item) {
                    return [
                        'product_name' => $item->product->name,
                        'product_image' => $item->product->image_url,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'formatted_price' => $item->formatted_price,
                        'subtotal' => $item->subtotal,
                        'formatted_subtotal' => $item->formatted_subtotal,
                    ];
                }),
                'total_items' => $order->getTotalItems(),
                'total_amount' => $order->total_amount,
                'formatted_total' => $order->formatted_total,
                'status' => $order->status_text,
                'created_at' => $order->created_at->format('d/m/Y H:i'),
            ],
        ], 200);
    }

    /**
     * Actualizar estado de orden (para admin)
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|integer|in:0,1,2,3,9',
        ], [
            'status.required' => 'El estado es obligatorio',
            'status.in' => 'Estado inválido',
        ]);

        try {
            $order = Order::findOrFail($id);
            $order->update(['status' => $validated['status']]);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente',
                'data' => [
                    'order_number' => $order->order_number,
                    'status' => $order->status_text,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar estado: ' . $e->getMessage(),
            ], 500);
        }
    }
}