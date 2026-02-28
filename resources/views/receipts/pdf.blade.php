<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran - {{ $transaction->code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
        }

        .receipt {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }

        .header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10px;
            margin: 2px 0;
        }

        .transaction-info {
            margin-bottom: 15px;
            font-size: 10px;
        }

        .transaction-info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .info-label {
            font-weight: normal;
        }

        .info-value {
            font-weight: bold;
            text-align: right;
        }

        .section-title {
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 8px;
            text-decoration: underline;
            font-size: 10px;
        }

        .items-separator {
            border-bottom: 1px dashed #000;
            margin: 10px 0;
        }

        .item {
            margin-bottom: 8px;
            font-size: 10px;
        }

        .item-name {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .item-detail {
            margin-left: 10px;
            font-size: 9px;
            display: flex;
            justify-content: space-between;
        }

        .totals {
            border-top: 2px dashed #000;
            border-bottom: 2px dashed #000;
            padding: 10px 0;
            margin: 15px 0;
            font-size: 11px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .total-row.grand-total {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 0;
        }

        .payment-info {
            font-size: 10px;
            margin-bottom: 15px;
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .footer {
            text-align: center;
            padding-top: 10px;
            border-top: 1px dashed #000;
            font-size: 9px;
            margin-top: 15px;
        }

        .footer-text {
            margin: 5px 0;
            font-weight: bold;
        }

        .footer-message {
            margin-top: 10px;
            font-style: italic;
        }

        .qr-section {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #000;
        }

        .qr-section p {
            font-size: 9px;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <h1>{{ $storeSettings['store_name'] }}</h1>
            <p>{{ $storeSettings['store_address'] }}</p>
            <p>Telp: {{ $storeSettings['store_phone'] }}</p>
        </div>

        <!-- Transaction Info -->
        <div class="transaction-info">
            <div class="transaction-info-row">
                <span class="info-label">No. Transaksi</span>
                <span class="info-value">{{ $transaction->code }}</span>
            </div>
            <div class="transaction-info-row">
                <span class="info-label">Tanggal</span>
                <span class="info-value">{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="transaction-info-row">
                <span class="info-label">Kasir</span>
                <span class="info-value">{{ $transaction->user?->name ?? '-' }}</span>
            </div>
            @if ($transaction->customer_phone)
                <div class="transaction-info-row">
                    <span class="info-label">Pelanggan</span>
                    <span class="info-value">{{ $transaction->customer_phone }}</span>
                </div>
            @endif
        </div>

        <!-- Items Section -->
        <div class="section-title">DETAIL PEMBELIAN</div>
        <div class="items-separator"></div>

        @foreach ($transaction->items as $item)
            <div class="item">
                <div class="item-name">{{ $item->product?->name ?? 'Produk' }}</div>
                <div class="item-detail">
                    <span>{{ $item->qty }} x {{ number_format($item->price, 0, ',', '.') }}</span>
                    <span>{{ number_format($item->total, 0, ',', '.') }}</span>
                </div>
            </div>
        @endforeach

        <div class="items-separator"></div>

        <!-- Totals Section -->
        <div class="totals">
            <div class="total-row">
                <span>Subtotal</span>
                <span>{{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
            </div>
            @if ($transaction->discount_total > 0)
                <div class="total-row">
                    <span>Diskon</span>
                    <span>-{{ number_format($transaction->discount_total, 0, ',', '.') }}</span>
                </div>
            @endif
            @if ($transaction->tax_total > 0)
                <div class="total-row">
                    <span>Pajak</span>
                    <span>{{ number_format($transaction->tax_total, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="total-row grand-total">
                <span>TOTAL</span>
                <span>{{ number_format($transaction->total, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="payment-info">
            <div class="payment-row">
                <span>Metode Pembayaran</span>
                <span style="text-align: right;">{{ $transaction->paymentMethod?->name ?? 'Cash' }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-text">{{ $storeSettings['receipt_header'] }}</div>
            @if ($storeSettings['receipt_footer'])
                <div class="footer-message">{{ $storeSettings['receipt_footer'] }}</div>
            @endif
        </div>

        <!-- Footer Message -->
        <div
            style="text-align: center; margin-top: 20px; padding-top: 15px; border-top: 1px dashed #000; font-size: 9px;">
            <p>Terima kasih atas pembelian Anda!</p>
            <p style="margin-top: 5px;">{{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>

</html>
