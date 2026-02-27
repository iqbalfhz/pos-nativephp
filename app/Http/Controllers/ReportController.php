<?php

namespace App\Http\Controllers;

use App\Exports\SalesExport;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function sales(Request $request): Response
    {
        $query = Transaction::query()
            ->with('user:id,name', 'paymentMethod:id,name');

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by payment method
        if ($request->filled('payment_method_id')) {
            $query->where('payment_method_id', $request->payment_method_id);
        }

        // Filter by cashier
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search by transaction code
        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        $transactions = $query
            ->orderByDesc('created_at')
            ->paginate(20)
            ->through(fn (Transaction $transaction) => [
                'id' => $transaction->id,
                'code' => $transaction->code,
                'total' => $transaction->total,
                'subtotal' => $transaction->subtotal,
                'discount_total' => $transaction->discount_total,
                'tax_total' => $transaction->tax_total,
                'payment_status' => $transaction->payment_status,
                'payment_method' => $transaction->paymentMethod?->name,
                'cashier' => $transaction->user?->name,
                'created_at' => $transaction->created_at->format('d/m/Y H:i'),
            ]);

        // Get filter options
        $paymentMethods = PaymentMethod::orderBy('name')->get(['id', 'name']);
        $cashiers = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Cashier', 'Admin', 'Super Admin']);
        })->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Reports/Sales', [
            'transactions' => $transactions,
            'paymentMethods' => $paymentMethods,
            'cashiers' => $cashiers,
            'filters' => $request->only(['search', 'start_date', 'end_date', 'payment_method_id', 'user_id', 'payment_status']),
        ]);
    }

    public function show(Transaction $transaction): Response
    {
        $transaction->load([
            'user:id,name',
            'paymentMethod:id,name',
            'items.product:id,name,sku,price',
        ]);

        return Inertia::render('Reports/Detail', [
            'transaction' => [
                'id' => $transaction->id,
                'code' => $transaction->code,
                'subtotal' => $transaction->subtotal,
                'discount_total' => $transaction->discount_total,
                'tax_total' => $transaction->tax_total,
                'total' => $transaction->total,
                'payment_status' => $transaction->payment_status,
                'payment_method' => $transaction->paymentMethod?->name,
                'cashier' => $transaction->user?->name,
                'notes' => $transaction->notes,
                'created_at' => $transaction->created_at->format('d/m/Y H:i:s'),
                'items' => $transaction->items->map(fn ($item) => [
                    'product_name' => $item->product->name,
                    'sku' => $item->product->sku,
                    'price' => $item->price,
                    'qty' => $item->qty,
                    'total' => $item->total,
                ]),
            ],
        ]);
    }

    public function exportSalesExcel(Request $request)
    {
        return Excel::download(
            new SalesExport($request->all()),
            'sales-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportSalesPdf(Request $request)
    {
        $query = Transaction::query()
            ->with('user:id,name', 'paymentMethod:id,name');

        // Apply same filters as sales() method
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('payment_method_id')) {
            $query->where('payment_method_id', $request->payment_method_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        $transactions = $query->orderByDesc('created_at')->get();

        $pdf = Pdf::loadView('reports.sales', [
            'transactions' => $transactions,
            'filters' => $request->all(),
        ]);

        return $pdf->download('sales-report-' . now()->format('Y-m-d') . '.pdf');
    }
}
