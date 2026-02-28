<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }

        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10px;
            color: #666;
        }

        .filters-info {
            font-size: 10px;
            margin-bottom: 15px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background: #2c3e50;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: 600;
            border: 1px solid #34495e;
        }

        td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        .currency {
            text-align: right;
            font-family: 'Courier New', monospace;
        }

        .footer {
            text-align: center;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #666;
        }

        .summary {
            float: right;
            width: 300px;
            background: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 11px;
        }

        .summary-total {
            font-weight: bold;
            border-top: 2px solid #333;
            padding-top: 8px !important;
            margin-top: 8px !important;
            font-size: 13px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Sales Report</h1>
        <p>Generated on {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    @if (!empty($filters))
        <div class="filters-info">
            <strong>Applied Filters:</strong><br>
            @if (!empty($filters['search']))
                Code: {{ $filters['search'] }} |
            @endif
            @if (!empty($filters['start_date']))
                From: {{ $filters['start_date'] }} |
            @endif
            @if (!empty($filters['end_date']))
                To: {{ $filters['end_date'] }} |
            @endif
            @if (!empty($filters['payment_method_id']))
                Payment Method: {{ $filters['payment_method_id'] }} |
            @endif
            @if (!empty($filters['user_id']))
                Cashier: {{ $filters['user_id'] }} |
            @endif
            @if (!empty($filters['payment_status']))
                Status: {{ $filters['payment_status'] }}
            @endif
        </div>
    @endif

    {{ $summaryHtml ?? '' }}

    <table>
        <thead>
            <tr>
                <th style="width: 12%;">Code</th>
                <th style="width: 15%;">Cashier</th>
                <th style="width: 15%;">Payment</th>
                <th style="width: 8%;">Status</th>
                <th class="currency" style="width: 12%;">Subtotal</th>
                <th class="currency" style="width: 10%;">Discount</th>
                <th class="currency" style="width: 10%;">Tax</th>
                <th class="currency" style="width: 12%;">Total</th>
                <th style="width: 18%;">Date</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalSubtotal = 0;
                $totalDiscount = 0;
                $totalTax = 0;
                $totalAmount = 0;
            @endphp

            @foreach ($transactions as $transaction)
                @php
                    $totalSubtotal += $transaction->subtotal;
                    $totalDiscount += $transaction->discount_total;
                    $totalTax += $transaction->tax_total;
                    $totalAmount += $transaction->total;
                @endphp
                <tr>
                    <td>{{ $transaction->code }}</td>
                    <td>{{ optional($transaction->user)->name ?? '-' }}</td>
                    <td>{{ optional($transaction->paymentMethod)->name ?? 'Cash' }}</td>
                    <td>
                        @if ($transaction->payment_status === 'paid')
                            <span style="background: #d4edda; padding: 2px 6px; border-radius: 3px;">PAID</span>
                        @else
                            <span style="background: #fff3cd; padding: 2px 6px; border-radius: 3px;">PENDING</span>
                        @endif
                    </td>
                    <td class="currency">{{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
                    <td class="currency">{{ number_format($transaction->discount_total, 0, ',', '.') }}</td>
                    <td class="currency">{{ number_format($transaction->tax_total, 0, ',', '.') }}</td>
                    <td class="currency" style="font-weight: bold;">
                        {{ number_format($transaction->total, 0, ',', '.') }}</td>
                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <div class="summary-row">
            <span>Subtotal:</span>
            <span>Rp {{ number_format($totalSubtotal, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row">
            <span>Total Discount:</span>
            <span>Rp {{ number_format($totalDiscount, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row">
            <span>Total Tax:</span>
            <span>Rp {{ number_format($totalTax, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row summary-total">
            <span>TOTAL SALES:</span>
            <span>Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
        </div>
    </div>

    <div style="clear: both;"></div>

    <div class="footer">
        <p>This report contains {{ count($transactions) }} transaction(s)</p>
        <p>Report generated at {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>

</html>
