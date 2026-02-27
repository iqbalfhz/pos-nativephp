<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
    transaction: {
        type: Object,
        required: true,
    },
    storeSettings: {
        type: Object,
        default: () => ({}),
    },
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
};

const formatDate = (date) => {
    return new Date(date).toLocaleString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const printTransaction = () => {
    window.print();
};
</script>

<template>
    <Head title="Detail Transaksi" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Detail Transaksi
                </h2>
                <div class="flex gap-2">
                    <button
                        @click="printTransaction"
                        class="rounded-md bg-white border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition print:hidden"
                    >
                        🖨️ Print
                    </button>
                    <Link
                        :href="route('reports.sales')"
                        class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700 transition print:hidden"
                    >
                        Kembali
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <!-- Receipt/Struk Content -->
                <div
                    class="bg-white shadow-lg rounded-lg p-8 print:shadow-none"
                >
                    <!-- Header -->
                    <div class="text-center border-b-2 border-dashed pb-4 mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">
                            {{ storeSettings.store_name || "POS Nativephp" }}
                        </h1>
                        <p class="text-sm text-gray-600 mt-1">
                            {{
                                storeSettings.store_address ||
                                "Jakarta, Indonesia"
                            }}
                        </p>
                        <p class="text-sm text-gray-600">
                            Telp:
                            {{ storeSettings.store_phone || "08123456789" }}
                        </p>
                    </div>

                    <!-- Transaction Info -->
                    <div class="space-y-2 mb-6 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">No. Transaksi</span>
                            <span class="font-semibold">{{
                                transaction.code
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tanggal</span>
                            <span>{{
                                formatDate(transaction.created_at)
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Kasir</span>
                            <span>{{ transaction.cashier || "-" }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Metode Pembayaran</span>
                            <span>{{ transaction.payment_method || "-" }}</span>
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="border-t-2 border-b-2 border-dashed py-4 mb-4">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left pb-2">Item</th>
                                    <th class="text-right pb-2">Qty</th>
                                    <th class="text-right pb-2">Harga</th>
                                    <th class="text-right pb-2">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(item, index) in transaction.items"
                                    :key="index"
                                    class="border-b border-gray-200"
                                >
                                    <td class="py-2">
                                        <div class="font-medium">
                                            {{ item.product_name }}
                                        </div>
                                        <div class="text-gray-500 text-xs">
                                            {{ item.sku }}
                                        </div>
                                    </td>
                                    <td class="text-right py-2">
                                        {{ item.qty }}
                                    </td>
                                    <td class="text-right py-2">
                                        {{ formatCurrency(item.price) }}
                                    </td>
                                    <td class="text-right font-medium py-2">
                                        {{ formatCurrency(item.total) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Totals -->
                    <div class="space-y-2 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span>{{
                                formatCurrency(transaction.subtotal)
                            }}</span>
                        </div>
                        <div
                            v-if="transaction.discount_total > 0"
                            class="flex justify-between text-sm"
                        >
                            <span class="text-gray-600">Diskon</span>
                            <span class="text-red-600"
                                >-{{
                                    formatCurrency(transaction.discount_total)
                                }}</span
                            >
                        </div>
                        <div
                            v-if="transaction.tax_total > 0"
                            class="flex justify-between text-sm"
                        >
                            <span class="text-gray-600">Pajak</span>
                            <span>{{
                                formatCurrency(transaction.tax_total)
                            }}</span>
                        </div>
                        <div
                            class="flex justify-between text-lg font-bold border-t-2 border-dashed pt-2"
                        >
                            <span>TOTAL</span>
                            <span>{{ formatCurrency(transaction.total) }}</span>
                        </div>
                    </div>

                    <!-- Payment Status -->
                    <div class="text-center text-sm text-gray-600 mb-6">
                        <p class="font-semibold">
                            {{
                                transaction.payment_status === "paid"
                                    ? "LUNAS"
                                    : "BELUM LUNAS"
                            }}
                        </p>
                    </div>

                    <!-- Notes -->
                    <div
                        v-if="transaction.notes"
                        class="text-sm text-gray-600 mb-6 border-t pt-4"
                    >
                        <p class="font-semibold">Catatan:</p>
                        <p class="mt-1">{{ transaction.notes }}</p>
                    </div>

                    <!-- Footer -->
                    <div
                        class="text-center text-sm text-gray-500 border-t-2 border-dashed pt-4"
                    >
                        <p class="font-semibold">
                            {{
                                storeSettings.receipt_header ||
                                "Terima Kasih Telah Berbelanja"
                            }}
                        </p>
                        <p class="mt-1">
                            {{
                                storeSettings.receipt_footer ||
                                "Barang yang sudah dibeli tidak dapat dikembalikan"
                            }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@media print {
    body * {
        visibility: hidden;
    }

    .bg-white,
    .bg-white * {
        visibility: visible;
    }

    .bg-white {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none !important;
        border-radius: 0 !important;
    }

    @page {
        margin: 1cm;
    }
}
</style>
