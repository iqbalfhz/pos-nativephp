<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({
    products: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const search = ref(props.filters.search || "");
const statusFilter = ref(props.filters.status || "");

watch([search, statusFilter], () => {
    router.get(
        route("stock.index"),
        {
            search: search.value,
            status: statusFilter.value,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
});

const resetFilters = () => {
    search.value = "";
    statusFilter.value = "";
};

const getStockBadge = (status) => {
    const badges = {
        out: "bg-red-100 text-red-800",
        low: "bg-yellow-100 text-yellow-800",
        good: "bg-green-100 text-green-800",
    };
    return badges[status] || badges.good;
};

const getStockLabel = (status) => {
    const labels = {
        out: "Habis",
        low: "Rendah",
        good: "Cukup",
    };
    return labels[status] || "Cukup";
};
</script>

<template>
    <Head title="Manajemen Stok" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Manajemen Stok
                </h2>
                <Link
                    :href="route('stock.history')"
                    class="rounded-md bg-white border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition"
                >
                    Riwayat Penyesuaian
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                    <!-- Filters -->
                    <div class="p-6 border-b bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                                    placeholder="Nama, SKU, atau barcode..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>
                            <div>
                                <label
                                    for="status"
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                >
                                    Status Stok
                                </label>
                                <select
                                    id="status"
                                    v-model="statusFilter"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">Semua Status</option>
                                    <option value="out">Stok Habis</option>
                                    <option value="low">Stok Rendah</option>
                                    <option value="good">Stok Cukup</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button
                                    type="button"
                                    class="w-full md:w-auto px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition"
                                    @click="resetFilters"
                                >
                                    Reset Filter
                                </button>
                            </div>
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
                                        Produk
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        SKU
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Kategori
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Stok Saat Ini
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Min Stok
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="product in products"
                                    :key="product.id"
                                    class="hover:bg-gray-50 transition"
                                >
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                    >
                                        {{ product.name }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"
                                    >
                                        {{ product.sku }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"
                                    >
                                        {{ product.category?.name || "-" }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-center"
                                    >
                                        <span
                                            class="font-semibold text-gray-900"
                                        >
                                            {{ product.stock }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-600"
                                    >
                                        {{ product.min_stock }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-center"
                                    >
                                        <span
                                            :class="[
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                getStockBadge(
                                                    product.stock_status,
                                                ),
                                            ]"
                                        >
                                            {{
                                                getStockLabel(
                                                    product.stock_status,
                                                )
                                            }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                                    >
                                        <Link
                                            :href="
                                                route(
                                                    'stock.adjust',
                                                    product.id,
                                                )
                                            "
                                            class="text-indigo-600 hover:text-indigo-900"
                                        >
                                            Sesuaikan Stok
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="products.length === 0">
                                    <td
                                        colspan="7"
                                        class="px-6 py-8 text-center text-gray-500"
                                    >
                                        Tidak ada produk yang ditemukan
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
