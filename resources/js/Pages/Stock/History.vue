<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({
    adjustments: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const search = ref(props.filters.search || "");
const typeFilter = ref(props.filters.type || "");
const startDate = ref(props.filters.start_date || "");
const endDate = ref(props.filters.end_date || "");

watch([search, typeFilter, startDate, endDate], () => {
    router.get(
        route("stock.history"),
        {
            search: search.value,
            type: typeFilter.value,
            start_date: startDate.value,
            end_date: endDate.value,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
});

const resetFilters = () => {
    search.value = "";
    typeFilter.value = "";
    startDate.value = "";
    endDate.value = "";
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
    <Head title="Riwayat Stok" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Riwayat Penyesuaian Stok
                </h2>
                <Link
                    :href="route('stock.index')"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    Kembali ke Stok
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                    <!-- Filters -->
                    <div class="p-6 border-b bg-gray-50">
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4"
                        >
                            <div>
                                <label
                                    for="search"
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                >
                                    Cari Produk
                                </label>
                                <input
                                    id="search"
                                    v-model="search"
                                    type="text"
                                    placeholder="Nama atau SKU..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>
                            <div>
                                <label
                                    for="type"
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                >
                                    Tipe
                                </label>
                                <select
                                    id="type"
                                    v-model="typeFilter"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">Semua Tipe</option>
                                    <option value="in">Stok Masuk</option>
                                    <option value="out">Stok Keluar</option>
                                    <option value="adjustment">
                                        Penyesuaian
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label
                                    for="start_date"
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                >
                                    Dari Tanggal
                                </label>
                                <input
                                    id="start_date"
                                    v-model="startDate"
                                    type="date"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>
                            <div>
                                <label
                                    for="end_date"
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                >
                                    Sampai Tanggal
                                </label>
                                <input
                                    id="end_date"
                                    v-model="endDate"
                                    type="date"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>
                        </div>
                        <div class="mt-4">
                            <button
                                type="button"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition"
                                @click="resetFilters"
                            >
                                Reset Filter
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Tanggal
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Produk
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Tipe
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Qty
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Stok
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Alasan
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Ref
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        User
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="adj in adjustments.data"
                                    :key="adj.id"
                                    class="hover:bg-gray-50 transition"
                                >
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"
                                    >
                                        {{ adj.created_at }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm"
                                    >
                                        <div class="font-medium text-gray-900">
                                            {{ adj.product.name }}
                                        </div>
                                        <div class="text-gray-500">
                                            {{ adj.product.sku }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="[
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                getTypeBadge(adj.type),
                                            ]"
                                        >
                                            {{ getTypeLabel(adj.type) }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold"
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
                                        class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-600"
                                    >
                                        {{ adj.stock_before }} →
                                        <span class="font-medium text-gray-900">
                                            {{ adj.stock_after }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ adj.reason }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        {{ adj.reference_number || "-" }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"
                                    >
                                        {{ adj.user }}
                                    </td>
                                </tr>
                                <tr v-if="adjustments.data.length === 0">
                                    <td
                                        colspan="8"
                                        class="px-6 py-8 text-center text-gray-500"
                                    >
                                        Tidak ada riwayat penyesuaian
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="adjustments.links.length > 3"
                        class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6"
                    >
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Menampilkan
                                <span class="font-medium">{{
                                    adjustments.from
                                }}</span>
                                sampai
                                <span class="font-medium">{{
                                    adjustments.to
                                }}</span>
                                dari
                                <span class="font-medium">{{
                                    adjustments.total
                                }}</span>
                                data
                            </div>
                            <div class="flex gap-1">
                                <Link
                                    v-for="link in adjustments.links"
                                    :key="link.label"
                                    :href="link.url"
                                    :class="[
                                        'px-3 py-2 text-sm border rounded',
                                        link.active
                                            ? 'bg-indigo-600 text-white border-indigo-600'
                                            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50',
                                        !link.url
                                            ? 'opacity-50 cursor-not-allowed'
                                            : '',
                                    ]"
                                    v-html="link.label"
                                >
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
