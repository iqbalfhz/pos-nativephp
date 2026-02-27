<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    recentAdjustments: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    type: "in",
    quantity: 1,
    reason: "",
    reference_number: "",
    notes: "",
});

const predictedStock = computed(() => {
    const currentStock = props.product.stock;
    const quantity = form.quantity || 0;

    if (form.type === "in" || form.type === "adjustment") {
        return currentStock + quantity;
    } else {
        return Math.max(0, currentStock - quantity);
    }
});

const submit = () => {
    form.post(route("stock.storeAdjustment", props.product.id), {
        onSuccess: () => form.reset(),
    });
};

const getTypeBadge = (type) => {
    const badges = {
        in: "bg-green-100 text-green-800",
        out: "bg-red-100 text-red-800",
        adjustment: "bg-blue-100 text-blue-800",
    };
    return badges[type] || badges.adjustment;
};

const getTypeLabel = (type) => {
    const labels = {
        in: "Masuk",
        out: "Keluar",
        adjustment: "Penyesuaian",
    };
    return labels[type] || type;
};
</script>

<template>
    <Head title="Sesuaikan Stok" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Sesuaikan Stok Produk
                </h2>
                <Link
                    :href="route('stock.index')"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    Kembali
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8 space-y-6">
                <!-- Product Info -->
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Informasi Produk
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Nama Produk</p>
                            <p class="font-medium text-gray-900">
                                {{ product.name }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">SKU</p>
                            <p class="font-medium text-gray-900">
                                {{ product.sku }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Stok Saat Ini</p>
                            <p class="text-2xl font-bold text-indigo-600">
                                {{ product.stock }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Stok Minimal</p>
                            <p class="font-medium text-gray-900">
                                {{ product.min_stock }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Adjustment Form -->
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Formulir Penyesuaian Stok
                    </h3>
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Type -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Tipe Penyesuaian
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-3 gap-3">
                                <label
                                    class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none"
                                    :class="
                                        form.type === 'in'
                                            ? 'border-indigo-500 ring-2 ring-indigo-500'
                                            : 'border-gray-300'
                                    "
                                >
                                    <input
                                        v-model="form.type"
                                        type="radio"
                                        value="in"
                                        class="sr-only"
                                    />
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span
                                                class="block text-sm font-medium text-gray-900"
                                            >
                                                Stok Masuk
                                            </span>
                                            <span
                                                class="mt-1 flex items-center text-sm text-gray-500"
                                            >
                                                Tambah stok
                                            </span>
                                        </span>
                                    </span>
                                </label>

                                <label
                                    class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none"
                                    :class="
                                        form.type === 'out'
                                            ? 'border-indigo-500 ring-2 ring-indigo-500'
                                            : 'border-gray-300'
                                    "
                                >
                                    <input
                                        v-model="form.type"
                                        type="radio"
                                        value="out"
                                        class="sr-only"
                                    />
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span
                                                class="block text-sm font-medium text-gray-900"
                                            >
                                                Stok Keluar
                                            </span>
                                            <span
                                                class="mt-1 flex items-center text-sm text-gray-500"
                                            >
                                                Kurangi stok
                                            </span>
                                        </span>
                                    </span>
                                </label>

                                <label
                                    class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none"
                                    :class="
                                        form.type === 'adjustment'
                                            ? 'border-indigo-500 ring-2 ring-indigo-500'
                                            : 'border-gray-300'
                                    "
                                >
                                    <input
                                        v-model="form.type"
                                        type="radio"
                                        value="adjustment"
                                        class="sr-only"
                                    />
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span
                                                class="block text-sm font-medium text-gray-900"
                                            >
                                                Penyesuaian
                                            </span>
                                            <span
                                                class="mt-1 flex items-center text-sm text-gray-500"
                                            >
                                                Koreksi stok
                                            </span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Quantity and Predicted -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label
                                    for="quantity"
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                >
                                    Jumlah
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="quantity"
                                    v-model.number="form.quantity"
                                    type="number"
                                    min="1"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                />
                                <p
                                    v-if="form.errors.quantity"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.quantity }}
                                </p>
                            </div>
                            <div class="flex items-end pb-2">
                                <div
                                    class="w-full p-4 bg-indigo-50 border border-indigo-200 rounded-md"
                                >
                                    <p class="text-sm text-indigo-700">
                                        Stok Setelah Penyesuaian
                                    </p>
                                    <p
                                        class="text-3xl font-bold text-indigo-900"
                                    >
                                        {{ predictedStock }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Reason -->
                        <div>
                            <label
                                for="reason"
                                class="block text-sm font-medium text-gray-700 mb-1"
                            >
                                Alasan
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="reason"
                                v-model="form.reason"
                                type="text"
                                placeholder="Contoh: Restock dari supplier, Produk rusak, dll"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            />
                            <p
                                v-if="form.errors.reason"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.reason }}
                            </p>
                        </div>

                        <!-- Reference Number -->
                        <div>
                            <label
                                for="reference_number"
                                class="block text-sm font-medium text-gray-700 mb-1"
                            >
                                Nomor Referensi
                            </label>
                            <input
                                id="reference_number"
                                v-model="form.reference_number"
                                type="text"
                                placeholder="Contoh: PO-2024-001, INV-12345"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>

                        <!-- Notes -->
                        <div>
                            <label
                                for="notes"
                                class="block text-sm font-medium text-gray-700 mb-1"
                            >
                                Catatan
                            </label>
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                rows="3"
                                placeholder="Catatan tambahan..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            ></textarea>
                        </div>

                        <!-- Submit Buttons -->
                        <div
                            class="flex items-center justify-end gap-3 pt-4 border-t"
                        >
                            <Link
                                :href="route('stock.index')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition"
                            >
                                Batal
                            </Link>
                            <button
                                type="submit"
                                class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-md hover:bg-indigo-700 transition"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">Menyimpan...</span>
                                <span v-else>Simpan Penyesuaian</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Recent Adjustments -->
                <div
                    v-if="recentAdjustments.length > 0"
                    class="bg-white shadow sm:rounded-lg p-6"
                >
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Riwayat Penyesuaian Terakhir
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Tanggal
                                    </th>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Tipe
                                    </th>
                                    <th
                                        class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Qty
                                    </th>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Stok
                                    </th>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Alasan
                                    </th>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        User
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="adj in recentAdjustments"
                                    :key="adj.id"
                                >
                                    <td
                                        class="px-4 py-3 whitespace-nowrap text-sm text-gray-600"
                                    >
                                        {{ adj.created_at }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span
                                            :class="[
                                                'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium',
                                                getTypeBadge(adj.type),
                                            ]"
                                        >
                                            {{ getTypeLabel(adj.type) }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium"
                                        :class="
                                            adj.quantity > 0
                                                ? 'text-green-600'
                                                : 'text-red-600'
                                        "
                                    >
                                        {{ adj.quantity > 0 ? "+" : ""
                                        }}{{ adj.quantity }}
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap text-sm text-gray-600"
                                    >
                                        {{ adj.stock_before }} →
                                        {{ adj.stock_after }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ adj.reason }}
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap text-sm text-gray-600"
                                    >
                                        {{ adj.user }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
