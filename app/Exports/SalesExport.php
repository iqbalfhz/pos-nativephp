<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): Collection
    {
        return Transaction::query()
            ->with('user:id,name', 'paymentMethod:id,name')
            ->orderByDesc('created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Code',
            'Cashier',
            'Payment Method',
            'Status',
            'Subtotal',
            'Discount',
            'Tax',
            'Total',
            'Date',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->code,
            $transaction->user?->name,
            $transaction->paymentMethod?->name,
            $transaction->payment_status,
            $transaction->subtotal,
            $transaction->discount_total,
            $transaction->tax_total,
            $transaction->total,
            $transaction->created_at?->toDateTimeString(),
        ];
    }
}
