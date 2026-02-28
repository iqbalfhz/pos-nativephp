<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SalesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection(): Collection
    {
        $query = Transaction::query()
            ->with('user:id,name', 'paymentMethod:id,name');

        // Apply filters
        if (!empty($this->filters['start_date'])) {
            $query->whereDate('created_at', '>=', $this->filters['start_date']);
        }

        if (!empty($this->filters['end_date'])) {
            $query->whereDate('created_at', '<=', $this->filters['end_date']);
        }

        if (!empty($this->filters['payment_method_id'])) {
            $query->where('payment_method_id', $this->filters['payment_method_id']);
        }

        if (!empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }

        if (!empty($this->filters['payment_status'])) {
            $query->where('payment_status', $this->filters['payment_status']);
        }

        if (!empty($this->filters['search'])) {
            $query->where('code', 'like', '%' . $this->filters['search'] . '%');
        }

        return $query->orderByDesc('created_at')->get();
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
            $transaction->created_at?->format('d/m/Y H:i'),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => '#,##0',
            'F' => '#,##0',
            'G' => '#,##0',
            'H' => '#,##0',
        ];
    }
}

