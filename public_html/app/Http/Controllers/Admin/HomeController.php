<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Post;
use App\Models\General; 
use App\Models\Coupon;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::pending()->count(),
            'completed_orders' => Order::completed()->count(),
            'total_users' => General::count(),
            'total_products' => Product::count(),
            'low_stock_products' => Product::where('stock', '<', 10)->count(),
            'total_stores' => Store::count(),
            'active_coupons' => Coupon::active()->count(),
        ];

        // Ventas del día
        $todaySales = Order::whereDate('created_at', Carbon::today())->sum('total_amount');

        // Ventas del mes
        $monthSales = Order::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');

        // Nuevos usuarios hoy
        $newUsersToday = General::whereDate('created_at', Carbon::today())->count();

        // Posts más vistos (top 5)
        $topPosts = Post::with(['product', 'staff'])
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get();

        // Productos con bajo stock
        $lowStockProducts = Product::where('stock', '<', 10)->orderBy('stock', 'asc')->limit(5)->get();

        // Últimas órdenes
        $recentOrders = Order::with(['general', 'items'])
            ->latest()
            ->limit(10)
            ->get();

        // Ventas por mes (últimos 6 meses) para gráfica
        $salesByMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $salesByMonth[] = [
                'month' => $date->format('M'),
                'sales' => Order::whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->sum('total_amount'),
            ];
        }

        // Órdenes por estado
        $ordersByStatus = [
            'pending' => Order::pending()->count(),
            'paid' => Order::paid()->count(),
            'shipped' => Order::shipped()->count(),
            'completed' => Order::completed()->count(),
            'cancelled' => Order::cancelled()->count(),
        ];

        return view('admin.pages.home', compact('stats', 'todaySales', 'monthSales', 'newUsersToday', 'topPosts', 'lowStockProducts', 'recentOrders', 'salesByMonth', 'ordersByStatus'));
    }
}
