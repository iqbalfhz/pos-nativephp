<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class StockController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Product::query()->with('category:id,name');

        // Filter by stock status
        if ($request->filled('status')) {
            if ($request->status === 'low') {
                $query->whereColumn('stock', '<=', 'min_stock');
            } elseif ($request->status === 'out') {
                $query->where('stock', 0);
            }
        }

        // Search by name or SKU
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $products = $query
            ->orderBy('name')
            ->get()
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'stock' => $product->stock,
                'min_stock' => $product->min_stock,
                'category' => $product->category?->only(['id', 'name']),
                'stock_status' => $product->stock === 0 ? 'out' : ($product->stock <= $product->min_stock ? 'low' : 'good'),
            ]);

        return Inertia::render('Stock/Index', [
            'products' => $products,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function adjust(Product $product): Response
    {
        $product->load('category:id,name');

        // Get recent adjustments for this product
        $recentAdjustments = StockAdjustment::where('product_id', $product->id)
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn ($adjustment) => [
                'id' => $adjustment->id,
                'type' => $adjustment->type,
                'quantity' => $adjustment->quantity,
                'stock_before' => $adjustment->stock_before,
                'stock_after' => $adjustment->stock_after,
                'reason' => $adjustment->reason,
                'reference_number' => $adjustment->reference_number,
                'notes' => $adjustment->notes,
                'user' => $adjustment->user->name,
                'created_at' => $adjustment->created_at->format('d/m/Y H:i'),
            ]);

        return Inertia::render('Stock/Adjust', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'stock' => $product->stock,
                'min_stock' => $product->min_stock,
                'category' => $product->category?->only(['id', 'name']),
            ],
            'recentAdjustments' => $recentAdjustments,
        ]);
    }

    public function storeAdjustment(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:in,out,adjustment'],
            'quantity' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string', 'max:255'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($validated, $product) {
            $stockBefore = $product->stock;
            $quantity = $validated['quantity'];

            // Calculate new stock based on type
            if ($validated['type'] === 'in' || $validated['type'] === 'adjustment') {
                $newStock = $stockBefore + $quantity;
            } else { // out
                $newStock = max(0, $stockBefore - $quantity);
            }

            // Create stock adjustment record
            StockAdjustment::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'type' => $validated['type'],
                'quantity' => $validated['type'] === 'out' ? -$quantity : $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $newStock,
                'reason' => $validated['reason'],
                'reference_number' => $validated['reference_number'],
                'notes' => $validated['notes'],
            ]);

            // Update product stock
            $product->update(['stock' => $newStock]);
        });

        return redirect()->route('stock.index')
            ->with('success', 'Stok berhasil disesuaikan');
    }

    public function history(Request $request): Response
    {
        $query = StockAdjustment::query()
            ->with(['product:id,name,sku', 'user:id,name']);

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Search by product name or SKU
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $adjustments = $query
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->through(fn ($adjustment) => [
                'id' => $adjustment->id,
                'product' => [
                    'name' => $adjustment->product->name,
                    'sku' => $adjustment->product->sku,
                ],
                'type' => $adjustment->type,
                'quantity' => $adjustment->quantity,
                'stock_before' => $adjustment->stock_before,
                'stock_after' => $adjustment->stock_after,
                'reason' => $adjustment->reason,
                'reference_number' => $adjustment->reference_number,
                'user' => $adjustment->user->name,
                'created_at' => $adjustment->created_at->format('d/m/Y H:i'),
            ]);

        return Inertia::render('Stock/History', [
            'adjustments' => $adjustments,
            'filters' => $request->only(['search', 'type', 'start_date', 'end_date']),
        ]);
    }
}
