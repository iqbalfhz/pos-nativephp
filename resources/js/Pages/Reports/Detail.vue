<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    transaction: {
        type: Object,
        required: true,
    },
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
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
                        class="rounded-md bg-white border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition"
                    >
                        Print
                    </button>
                    <Link
                        :href="route('reports.sales')"
                        class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700 transition"
                    >
                        Kembali
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8 space-y-6">
                <!-- Transaction Info -->
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Informasi Transaksi
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Kode Transaksi</p>
                            <p class="font-medium text-gray-900">
                                {{ transaction.code }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal</p>
                            <p class="font-medium text-gray-900">
                                {{ transaction.created_at }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Kasir</p>
                            <p class="font-medium text-gray-900">
                                {{ transaction.cashier }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">
                                Metode Pembayaran
                            </p>
                            <p class="font-medium text-gray-900">
                                {{ transaction.payment_method }}
                            </p>
                        </div>
                    </div>
                    <div v-if="transaction.notes" class="mt-4">
                        <p class="text-sm text-gray-500">Catatan</p>
                        <p class="text-gray-900">{{ transaction.notes }}</p>
                    </div>
                </div>

                <!-- Items -->
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Item Transaksi
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Produk
                                    </th>
                                    <th
                                        class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Harga
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Qty
                                    </th>
                                    <th
                                        class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Subtotal
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="(item, index) in transaction.items"
                                    :key="index"
                                >
                                    <td class="px-4 py-3 text-sm">
                                        <div class="font-medium text-gray-900">
                                            {{ item.product_name }}
                                        </div>
                                        <div class="text-gray-500">
                                            {{ item.sku }}
                                        </div>
                                    </td>
                                    <td
                                        class="px-4 py-3 text-sm text-right text-gray-600"
                                    >
                                        {{ formatCurrency(item.price) }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-sm text-center font-medium text-gray-900"
                                    >
                                        {{ item.qty }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-sm text-right font-medium text-gray-900"
                                    >
                                        {{ formatCurrency(item.total) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Summary -->
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Ringkasan Pembayaran
                    </h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium text-gray-900">
                                {{ formatCurrency(transaction.subtotal) }}
                            </span>
                        </div>
                        <div
                            v-if="transaction.discount_total > 0"
                            class="flex justify-between text-sm"
                        >
                            <span class="text-gray-600">Diskon</span>
                            <span class="font-medium text-red-600">
                                -{{
                                    formatCurrency(transaction.discount_total)
                                }}
                            </span>
                        </div>
                        <div
                            v-if="transaction.tax_total > 0"
                            class="flex justify-between text-sm"
                        >
                            <span class="text-gray-600">PB1 (10%)</span>
                            <span class="font-medium text-gray-900">
                                {{ formatCurrency(transaction.tax_total) }}
                            </span>
                        </div>
                        <div
                            class="flex justify-between text-lg font-semibold pt-2 border-t"
                        >
                            <span class="text-gray-900">Total</span>
                            <span class="text-indigo-600">
                                {{ formatCurrency(transaction.total) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@media print {
    nav,
    header button,
    .no-print {
        display: none;
    }
}
</style>
