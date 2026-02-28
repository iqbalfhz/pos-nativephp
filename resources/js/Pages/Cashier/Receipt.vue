<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { computed, ref } from "vue";

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

const isLoadingPdf = ref(false);

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

const downloadPdf = () => {
    window.location.href = route(
        "cashier.receipt.export-pdf",
        props.transaction.id,
    );
};

const sendReceiptViaWhatsApp = async () => {
    const phone = props.transaction.customer_phone || "";
    const cleanPhone = phone.replace(/\D/g, "");

    if (!cleanPhone) {
        alert("Nomor telepon pelanggan belum tersedia");
        return;
    }

    isLoadingPdf.value = true;

    try {
        // Generate PDF and get download URL
        const response = await fetch(
            route("cashier.receipt.generate-pdf", props.transaction.id),
            {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content,
                    "Content-Type": "application/json",
                },
            },
        );

        if (!response.ok) {
            throw new Error("Gagal generate PDF");
        }

        const data = await response.json();
        const pdfUrl = data.download_url;

        // Create message with PDF link
        let msg = `*STRUK PEMBAYARAN*\n\n`;
        msg += `No. Transaksi: ${props.transaction.code}\n`;
        msg += `Tanggal: ${formatDate(props.transaction.created_at)}\n`;
        msg += `Kasir: ${props.transaction.user?.name || "-"}\n\n`;
        msg += `📎 Unduh struk: ${pdfUrl}\n\n`;
        msg += `*DETAIL PEMBELIAN*\n`;

        props.transaction.items.forEach((item) => {
            msg += `${item.product?.name || "Produk"}\n`;
            msg += `  ${item.qty} x ${formatCurrency(item.price)} = ${formatCurrency(item.total)}\n`;
        });

        msg += `\n`;
        msg += `Subtotal: ${formatCurrency(props.transaction.subtotal)}\n`;
        if (props.transaction.discount_total > 0) {
            msg += `Diskon: ${formatCurrency(props.transaction.discount_total)}\n`;
        }
        if (props.transaction.tax_total > 0) {
            msg += `Pajak: ${formatCurrency(props.transaction.tax_total)}\n`;
        }
        msg += `*Total: ${formatCurrency(props.transaction.total)}*\n\n`;
        msg += `Metode Pembayaran: ${props.transaction.payment_method?.name || "Cash"}\n`;
        msg += `\nTerima kasih atas pembelian Anda!`;

        const whatsappUrl = `https://wa.me/${cleanPhone}?text=${encodeURIComponent(msg)}`;
        window.open(whatsappUrl, "_blank");
    } catch (error) {
        console.error("Error:", error);
        alert("Gagal generate PDF: " + error.message);
    } finally {
        isLoadingPdf.value = false;
    }
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
                <div class="mb-6 flex flex-wrap gap-3 print:hidden">
                    <button
                        @click="printReceipt"
                        class="flex-1 min-w-[120px] rounded-lg bg-blue-600 px-4 py-3 text-white font-semibold hover:bg-blue-700 transition text-sm md:text-base"
                    >
                        🖨️ Print Struk
                    </button>
                    <button
                        @click="downloadPdf"
                        class="flex-1 min-w-[120px] rounded-lg bg-red-600 px-4 py-3 text-white font-semibold hover:bg-red-700 transition text-sm md:text-base"
                    >
                        📄 Download PDF
                    </button>
                    <button
                        v-if="transaction.customer_phone"
                        @click="sendReceiptViaWhatsApp"
                        :disabled="isLoadingPdf"
                        class="flex-1 min-w-[120px] rounded-lg bg-green-600 px-4 py-3 text-white font-semibold hover:bg-green-700 transition text-sm md:text-base disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{
                            isLoadingPdf
                                ? "⏳ Generating..."
                                : "📱 Kirim via WhatsApp"
                        }}
                    </button>
                    <Link
                        :href="route('cashier.index')"
                        class="flex-1 min-w-[100px] rounded-lg bg-gray-600 px-4 py-3 text-white font-semibold hover:bg-gray-700 transition text-center text-sm md:text-base"
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
