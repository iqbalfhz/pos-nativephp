<?php

namespace App\Http\Controllers;

use App\Exports\SalesExport;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function sales(Request $request): Response
    {
        $transactions = Transaction::query()
            ->with('user:id,name', 'paymentMethod:id,name')
            ->orderByDesc('created_at')
            ->limit(100)
            ->get()
            ->map(fn (Transaction $transaction) => [
                'id' => $transaction->id,
                'code' => $transaction->code,
                'total' => $transaction->total,
                'payment_status' => $transaction->payment_status,
                'payment_method' => $transaction->paymentMethod?->name,
                'cashier' => $transaction->user?->name,
                'created_at' => $transaction->created_at->toDateTimeString(),
            ]);

        return Inertia::render('Reports/Sales', [
            'transactions' => $transactions,
        ]);
    }

    public function exportSalesExcel()
    {
        return Excel::download(new SalesExport(), 'sales-report.xlsx');
    }

    public function exportSalesPdf()
    {
        $transactions = Transaction::query()
            ->with('user:id,name', 'paymentMethod:id,name')
            ->orderByDesc('created_at')
            ->get();

        $pdf = Pdf::loadView('reports.sales', [
            'transactions' => $transactions,
        ]);

        return $pdf->download('sales-report.pdf');
    }
}
