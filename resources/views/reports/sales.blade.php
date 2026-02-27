<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #222;
        }

        h1 {
            font-size: 16px;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #f3f4f6;
        }
    </style>
</head>

<body>
    <h1>Sales Report</h1>
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Cashier</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Subtotal</th>
                <th>Discount</th>
                <th>Tax</th>
                <th>Total</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->code }}</td>
                    <td>{{ optional($transaction->user)->name }}</td>
                    <td>{{ optional($transaction->paymentMethod)->name }}</td>
                    <td>{{ $transaction->payment_status }}</td>
                    <td>{{ $transaction->subtotal }}</td>
                    <td>{{ $transaction->discount_total }}</td>
                    <td>{{ $transaction->tax_total }}</td>
                    <td>{{ $transaction->total }}</td>
                    <td>{{ $transaction->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
