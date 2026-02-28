<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\SettingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class CashierController extends Controller
{
    public function index(): InertiaResponse
    {
        $paymentMethods = PaymentMethod::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'type']);
        
        $cashPaymentMethod = $paymentMethods->firstWhere('type', 'cash');
        
        return Inertia::render('Cashier/Index', [
            'products' => Product::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'sku', 'barcode', 'price', 'stock']),
            'paymentMethods' => $paymentMethods,
            'defaultPaymentMethodId' => $cashPaymentMethod?->id,
        ]);
    }

    public function checkout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'payment_method_id' => ['nullable', 'exists:payment_methods,id'],
            'customer_phone' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:500'],
            'discount_total' => ['nullable', 'numeric', 'min:0'],
            'tax_total' => ['nullable', 'numeric', 'min:0'],
        ]);

        $items = collect($validated['items']);
        $productIds = $items->pluck('product_id')->all();
        $products = Product::query()->whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($items as $item) {
            $product = $products->get($item['product_id']);

            if (! $product || $product->stock < $item['qty']) {
                return back()->withErrors([
                    'items' => 'Stok tidak mencukupi untuk salah satu produk.',
                ]);
            }
        }

        $transaction = DB::transaction(function () use ($request, $validated, $items, $products) {
            $subtotal = 0;

            foreach ($items as $item) {
                $product = $products->get($item['product_id']);
                $subtotal += $product->price * $item['qty'];
            }

            $discountTotal = $validated['discount_total'] ?? 0;
            $taxTotal = $validated['tax_total'] ?? 0;
            $total = $subtotal - $discountTotal + $taxTotal;

            $transaction = Transaction::create([
                'code' => $this->generateCode(),
                'user_id' => $request->user()->id,
                'subtotal' => $subtotal,
                'discount_total' => $discountTotal,
                'tax_total' => $taxTotal,
                'total' => $total,
                'payment_method_id' => $validated['payment_method_id'] ?? null,
                'payment_status' => 'paid',
                'customer_phone' => $validated['customer_phone'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                $product = $products->get($item['product_id']);
                $lineTotal = $product->price * $item['qty'];

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'price' => $product->price,
                    'discount' => 0,
                    'total' => $lineTotal,
                ]);

                $product->decrement('stock', $item['qty']);
            }

            return $transaction;
        });

        return redirect()->route('cashier.receipt', $transaction->id);
    }

    public function receipt(Transaction $transaction): InertiaResponse
    {
        $transaction->load(['items.product', 'user', 'paymentMethod']);

        return Inertia::render('Cashier/Receipt', [
            'transaction' => $transaction,
            'storeSettings' => [
                'store_name' => SettingService::get('store_name', 'POS Nativephp'),
                'store_address' => SettingService::get('store_address', 'Jakarta, Indonesia'),
                'store_phone' => SettingService::get('store_phone', '08123456789'),
                'receipt_header' => SettingService::get('receipt_header', 'Terima Kasih Telah Berbelanja'),
                'receipt_footer' => SettingService::get('receipt_footer', 'Barang yang sudah dibeli tidak dapat dikembalikan'),
            ],
        ]);
    }

    public function exportPdf(Transaction $transaction): Response
    {
        $transaction->load(['items.product', 'user', 'paymentMethod']);
        
        $storeSettings = [
            'store_name' => SettingService::get('store_name', 'POS Nativephp'),
            'store_address' => SettingService::get('store_address', 'Jakarta, Indonesia'),
            'store_phone' => SettingService::get('store_phone', '08123456789'),
            'receipt_header' => SettingService::get('receipt_header', 'Terima Kasih Telah Berbelanja'),
            'receipt_footer' => SettingService::get('receipt_footer', 'Barang yang sudah dibeli tidak dapat dikembalikan'),
        ];

        $pdf = Pdf::loadView('receipts.pdf', [
            'transaction' => $transaction,
            'storeSettings' => $storeSettings,
        ]);

        return $pdf->download('struk-'.$transaction->code.'.pdf');
    }

    public function generateReceiptPdf(Transaction $transaction): JsonResponse
    {
        $transaction->load(['items.product', 'user', 'paymentMethod']);
        
        $storeSettings = [
            'store_name' => SettingService::get('store_name', 'POS Nativephp'),
            'store_address' => SettingService::get('store_address', 'Jakarta, Indonesia'),
            'store_phone' => SettingService::get('store_phone', '08123456789'),
            'receipt_header' => SettingService::get('receipt_header', 'Terima Kasih Telah Berbelanja'),
            'receipt_footer' => SettingService::get('receipt_footer', 'Barang yang sudah dibeli tidak dapat dikembalikan'),
        ];

        $pdf = Pdf::loadView('receipts.pdf', [
            'transaction' => $transaction,
            'storeSettings' => $storeSettings,
        ]);

        // Create directory if not exists
        if (!Storage::disk('public')->exists('receipts')) {
            Storage::disk('public')->makeDirectory('receipts');
        }

        $filename = 'struk-'.$transaction->code.'.pdf';
        $path = 'receipts/'.$filename;

        // Save PDF to public storage
        Storage::disk('public')->put($path, $pdf->output());

        // Generate URL
        $downloadUrl = url('/storage/'.$path);

        return response()->json([
            'success' => true,
            'download_url' => $downloadUrl,
            'filename' => $filename,
        ]);
    }

    private function generateCode(): string
    {
        return 'TRX-'.now()->format('Ymd-His').'-'.Str::upper(Str::random(4));
    }
}
