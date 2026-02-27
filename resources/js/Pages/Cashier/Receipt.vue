<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { computed } from "vue";

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

const printReceipt = () => {
    window.print();
};

const sendWhatsApp = () => {
    const phone = props.transaction.customer_phone || "";
    const message = generateWhatsAppMessage();
    const url = `https://wa.me/${phone.replace(/\D/g, "")}?text=${encodeURIComponent(message)}`;
    window.open(url, "_blank");
};

const generateWhatsAppMessage = () => {
    const t = props.transaction;
    let msg = `*STRUK PEMBAYARAN*\n\n`;
    msg += `No. Transaksi: ${t.code}\n`;
    msg += `Tanggal: ${formatDate(t.created_at)}\n`;
    msg += `Kasir: ${t.user?.name || "-"}\n\n`;
    msg += `*DETAIL PEMBELIAN*\n`;

    t.items.forEach((item) => {
        msg += `${item.product?.name || "Produk"}\n`;
        msg += `  ${item.qty} x ${formatCurrency(item.price)} = ${formatCurrency(item.total)}\n`;
    });

    msg += `\n`;
    msg += `Subtotal: ${formatCurrency(t.subtotal)}\n`;
    if (t.discount_total > 0) {
        msg += `Diskon: ${formatCurrency(t.discount_total)}\n`;
    }
    if (t.tax_total > 0) {
        msg += `Pajak: ${formatCurrency(t.tax_total)}\n`;
    }
    msg += `*Total: ${formatCurrency(t.total)}*\n\n`;
    msg += `Metode Pembayaran: ${t.payment_method?.name || "Cash"}\n`;
    msg += `\nTerima kasih atas pembelian Anda!`;

    return msg;
};
</script>

<template>
    <Head title="Struk Transaksi" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Struk Transaksi
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <!-- Action Buttons (Hide on Print) -->
                <div class="mb-6 flex gap-3 print:hidden">
                    <button
                        @click="printReceipt"
                        class="flex-1 rounded-lg bg-blue-600 px-6 py-3 text-white font-semibold hover:bg-blue-700 transition"
                    >
                        🖨️ Print Struk
                    </button>
                    <button
                        v-if="transaction.customer_phone"
                        @click="sendWhatsApp"
                        class="flex-1 rounded-lg bg-green-600 px-6 py-3 text-white font-semibold hover:bg-green-700 transition"
                    >
                        📱 Kirim via WhatsApp
                    </button>
                    <Link
                        :href="route('cashier.index')"
                        class="rounded-lg bg-gray-600 px-6 py-3 text-white font-semibold hover:bg-gray-700 transition text-center"
                    >
                        Kembali
                    </Link>
                </div>

                <!-- Receipt Content -->
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
                            <span>{{ transaction.user?.name || "-" }}</span>
                        </div>
                        <div
                            v-if="transaction.customer_phone"
                            class="flex justify-between"
                        >
                            <span class="text-gray-600">Customer</span>
                            <span>{{ transaction.customer_phone }}</span>
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
                                    v-for="item in transaction.items"
                                    :key="item.id"
                                    class="border-b border-gray-200"
                                >
                                    <td class="py-2">
                                        {{ item.product?.name || "Produk" }}
                                    </td>
                                    <td class="text-right">{{ item.qty }}</td>
                                    <td class="text-right">
                                        {{ formatCurrency(item.price) }}
                                    </td>
                                    <td class="text-right font-medium">
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

                    <!-- Payment Method -->
                    <div class="text-center text-sm text-gray-600 mb-6">
                        <p>
                            Metode Pembayaran:
                            <span class="font-semibold">{{
                                transaction.payment_method?.name || "Cash"
                            }}</span>
                        </p>
                        <p class="text-green-600 font-semibold mt-1">
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
