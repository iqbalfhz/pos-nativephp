<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    stats: Object,
    low_stock_products: Array,
    top_products: Array,
    sales_chart: Array,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("id-ID", {
        day: "numeric",
        month: "short",
    });
};

const chartLabels = computed(() => {
    return props.sales_chart.map((item) => formatDate(item.date));
});

const chartData = computed(() => {
    return props.sales_chart.map((item) => item.total);
});

const maxChartValue = computed(() => {
    return Math.max(...chartData.value, 1);
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- Stats Cards -->
                <div
                    class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4"
                >
                    <!-- Today Sales -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg
                                        class="h-6 w-6 text-blue-600"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt
                                            class="text-sm font-medium text-gray-500 truncate"
                                        >
                                            Penjualan Hari Ini
                                        </dt>
                                        <dd>
                                            <div
                                                class="text-lg font-semibold text-gray-900"
                                            >
                                                {{
                                                    formatCurrency(
                                                        stats.today_sales,
                                                    )
                                                }}
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <span class="font-medium text-gray-500">
                                    {{ stats.today_transactions }} transaksi
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Sales -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg
                                        class="h-6 w-6 text-green-600"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                        />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt
                                            class="text-sm font-medium text-gray-500 truncate"
                                        >
                                            Penjualan Bulan Ini
                                        </dt>
                                        <dd>
                                            <div
                                                class="text-lg font-semibold text-gray-900"
                                            >
                                                {{
                                                    formatCurrency(
                                                        stats.monthly_sales,
                                                    )
                                                }}
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <span class="font-medium text-gray-500">
                                    {{ stats.monthly_transactions }} transaksi
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Total Products -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg
                                        class="h-6 w-6 text-purple-600"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                        />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt
                                            class="text-sm font-medium text-gray-500 truncate"
                                        >
                                            Total Produk
                                        </dt>
                                        <dd>
                                            <div
                                                class="text-lg font-semibold text-gray-900"
                                            >
                                                {{ stats.total_products }}
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <Link
                                    :href="route('products.index')"
                                    class="font-medium text-purple-600 hover:text-purple-500"
                                >
                                    Lihat semua
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Total Categories -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg
                                        class="h-6 w-6 text-yellow-600"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                                        />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt
                                            class="text-sm font-medium text-gray-500 truncate"
                                        >
                                            Total Kategori
                                        </dt>
                                        <dd>
                                            <div
                                                class="text-lg font-semibold text-gray-900"
                                            >
                                                {{ stats.total_categories }}
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <Link
                                    :href="route('categories.index')"
                                    class="font-medium text-yellow-600 hover:text-yellow-500"
                                >
                                    Lihat semua
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts & Tables Row -->
                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                    <!-- Sales Chart -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Grafik Penjualan (7 Hari Terakhir)
                        </h3>
                        <div class="space-y-3">
                            <div
                                v-for="(value, index) in chartData"
                                :key="index"
                                class="flex items-center"
                            >
                                <div class="w-20 text-sm text-gray-600">
                                    {{ chartLabels[index] }}
                                </div>
                                <div class="flex-1 ml-3">
                                    <div class="flex items-center">
                                        <div
                                            class="h-6 bg-blue-500 rounded"
                                            :style="{
                                                width:
                                                    (value / maxChartValue) *
                                                        100 +
                                                    '%',
                                            }"
                                        ></div>
                                        <span
                                            class="ml-2 text-sm font-medium text-gray-700"
                                        >
                                            {{ formatCurrency(value) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div
                                v-if="chartData.length === 0"
                                class="text-center text-gray-500 py-8"
                            >
                                Belum ada data penjualan
                            </div>
                        </div>
                    </div>

                    <!-- Top Products -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Produk Terlaris (7 Hari)
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th
                                            class="text-left text-xs font-medium text-gray-500 uppercase"
                                        >
                                            Produk
                                        </th>
                                        <th
                                            class="text-right text-xs font-medium text-gray-500 uppercase"
                                        >
                                            Terjual
                                        </th>
                                        <th
                                            class="text-right text-xs font-medium text-gray-500 uppercase"
                                        >
                                            Revenue
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr
                                        v-for="item in top_products"
                                        :key="item.product_id"
                                    >
                                        <td class="py-3 text-sm">
                                            <div
                                                class="font-medium text-gray-900"
                                            >
                                                {{ item.product?.name }}
                                            </div>
                                            <div class="text-gray-500">
                                                {{ item.product?.sku }}
                                            </div>
                                        </td>
                                        <td
                                            class="py-3 text-sm text-right font-medium"
                                        >
                                            {{ item.total_sold }}
                                        </td>
                                        <td class="py-3 text-sm text-right">
                                            {{ formatCurrency(item.revenue) }}
                                        </td>
                                    </tr>
                                    <tr v-if="top_products.length === 0">
                                        <td
                                            colspan="3"
                                            class="py-8 text-center text-gray-500"
                                        >
                                            Belum ada data
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Products -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Stok Rendah
                        </h3>
                        <Link
                            :href="route('products.index')"
                            class="text-sm text-blue-600 hover:text-blue-500"
                        >
                            Lihat semua produk
                        </Link>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th
                                        class="text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Produk
                                    </th>
                                    <th
                                        class="text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        SKU
                                    </th>
                                    <th
                                        class="text-center text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Stok Saat Ini
                                    </th>
                                    <th
                                        class="text-center text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Stok Minimal
                                    </th>
                                    <th
                                        class="text-center text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr
                                    v-for="product in low_stock_products"
                                    :key="product.id"
                                >
                                    <td
                                        class="py-3 text-sm font-medium text-gray-900"
                                    >
                                        {{ product.name }}
                                    </td>
                                    <td class="py-3 text-sm text-gray-500">
                                        {{ product.sku }}
                                    </td>
                                    <td class="py-3 text-sm text-center">
                                        <span
                                            :class="[
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                product.stock === 0
                                                    ? 'bg-red-100 text-red-800'
                                                    : 'bg-yellow-100 text-yellow-800',
                                            ]"
                                        >
                                            {{ product.stock }}
                                        </span>
                                    </td>
                                    <td
                                        class="py-3 text-sm text-center text-gray-500"
                                    >
                                        {{ product.min_stock }}
                                    </td>
                                    <td class="py-3 text-sm text-center">
                                        <span
                                            v-if="product.stock === 0"
                                            class="text-red-600 font-medium"
                                        >
                                            Habis
                                        </span>
                                        <span
                                            v-else
                                            class="text-yellow-600 font-medium"
                                        >
                                            Rendah
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="low_stock_products.length === 0">
                                    <td
                                        colspan="5"
                                        class="py-8 text-center text-gray-500"
                                    >
                                        Semua produk memiliki stok yang cukup
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
