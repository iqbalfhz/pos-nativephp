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
                DB::raw("strftime('%Y-%m-%d', created_at) as date"),
                DB::raw('SUM(total) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Kategori terlaris (7 hari terakhir)
        $topCategories = DB::table('transaction_items')
            ->select(
                'p.category_id',
                'c.name',
                DB::raw('SUM(transaction_items.total) as revenue'),
                DB::raw('SUM(transaction_items.qty) as total_sold')
            )
            ->join('products as p', 'transaction_items.product_id', '=', 'p.id')
            ->join('categories as c', 'p.category_id', '=', 'c.id')
            ->join('transactions as t', 'transaction_items.transaction_id', '=', 't.id')
            ->where('t.created_at', '>=', $weekAgo)
            ->where('t.payment_status', 'paid')
            ->groupBy('p.category_id', 'c.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();
        
        // Payment method distribution (7 hari terakhir)
        $paymentDistribution = Transaction::where('created_at', '>=', $weekAgo)
            ->where('payment_status', 'paid')
            ->select(
                'payment_method_id',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total')
            )
            ->with('paymentMethod:id,name')
            ->groupBy('payment_method_id')
            ->get();
        
        // Monthly sales comparison (last 12 months)
        $monthlyChart = Transaction::where('created_at', '>=', now()->subMonths(12)->startOfMonth())
            ->where('payment_status', 'paid')
            ->select(
                DB::raw("strftime('%Y-%m', created_at) as month"),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
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
            'top_categories' => $topCategories,
            'payment_distribution' => $paymentDistribution,
            'sales_chart' => $salesChart,
            'monthly_chart' => $monthlyChart,
        ]);
    }
}
