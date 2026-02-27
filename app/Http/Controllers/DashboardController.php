<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        // Stats hari ini
        $today = now()->startOfDay();
        
        $todaySales = Transaction::where('created_at', '>=', $today)
            ->where('payment_status', 'paid')
            ->sum('total');
        
        $todayTransactions = Transaction::where('created_at', '>=', $today)
            ->where('payment_status', 'paid')
            ->count();
        
        // Stats bulan ini
        $monthStart = now()->startOfMonth();
        
        $monthlySales = Transaction::where('created_at', '>=', $monthStart)
            ->where('payment_status', 'paid')
            ->sum('total');
        
        $monthlyTransactions = Transaction::where('created_at', '>=', $monthStart)
            ->where('payment_status', 'paid')
            ->count();
        
        // Produk dengan stok rendah (≤ min_stock)
        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')
            ->where('is_active', true)
            ->orderBy('stock')
            ->limit(10)
            ->get(['id', 'name', 'sku', 'stock', 'min_stock']);
        
        // Produk terlaris (7 hari terakhir)
        $weekAgo = now()->subDays(7);
        $topProducts = TransactionItem::query()
            ->select('product_id', DB::raw('SUM(qty) as total_sold'), DB::raw('SUM(total) as revenue'))
            ->whereHas('transaction', function ($query) use ($weekAgo) {
                $query->where('created_at', '>=', $weekAgo)
                    ->where('payment_status', 'paid');
            })
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->with('product:id,name,sku,price')
            ->get();
        
        // Grafik penjualan 7 hari terakhir
        $salesChart = Transaction::where('created_at', '>=', $weekAgo)
            ->where('payment_status', 'paid')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Total produk dan kategori
        $totalProducts = Product::where('is_active', true)->count();
        $totalCategories = DB::table('categories')->count();
        
        return Inertia::render('Dashboard', [
            'stats' => [
                'today_sales' => $todaySales,
                'today_transactions' => $todayTransactions,
                'monthly_sales' => $monthlySales,
                'monthly_transactions' => $monthlyTransactions,
                'total_products' => $totalProducts,
                'total_categories' => $totalCategories,
            ],
            'low_stock_products' => $lowStockProducts,
            'top_products' => $topProducts,
            'sales_chart' => $salesChart,
        ]);
    }
}
