<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, watch, computed } from "vue";

const props = defineProps({
    transactions: {
        type: Object,
        required: true,
    },
    paymentMethods: {
        type: Array,
        default: () => [],
    },
    cashiers: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const search = ref(props.filters.search || "");
const startDate = ref(props.filters.start_date || "");
const endDate = ref(props.filters.end_date || "");
const paymentMethodId = ref(props.filters.payment_method_id || "");
const userId = ref(props.filters.user_id || "");
const paymentStatus = ref(props.filters.payment_status || "");

watch(
    [search, startDate, endDate, paymentMethodId, userId, paymentStatus],
    () => {
        router.get(
            route("reports.sales"),
            {
                search: search.value,
                start_date: startDate.value,
                end_date: endDate.value,
                payment_method_id: paymentMethodId.value,
                user_id: userId.value,
                payment_status: paymentStatus.value,
            },
            {
                preserveState: true,
                replace: true,
            },
        );
    },
);

const resetFilters = () => {
    search.value = "";
    startDate.value = "";
    endDate.value = "";
    paymentMethodId.value = "";
    userId.value = "";
    paymentStatus.value = "";
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
};

const getStatusBadge = (status) => {
    return status === "paid"
        ? "bg-green-100 text-green-800"
        : "bg-yellow-100 text-yellow-800";
};

const exportUrl = computed(() => {
    const params = new URLSearchParams();
    if (search.value) params.append("search", search.value);
    if (startDate.value) params.append("start_date", startDate.value);
    if (endDate.value) params.append("end_date", endDate.value);
    if (paymentMethodId.value)
        params.append("payment_method_id", paymentMethodId.value);
    if (userId.value) params.append("user_id", userId.value);
    if (paymentStatus.value)
        params.append("payment_status", paymentStatus.value);
    return params.toString();
});
</script>

<template>
    <Head title="Laporan Penjualan" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Laporan Penjualan
                </h2>
                <div class="flex gap-2">
                    <a
                        :href="
                            route('reports.sales.excel') +
                            (exportUrl ? '?' + exportUrl : '')
                        "
                        class="rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 transition"
                    >
                        Excel
                    </a>
                    <a
                        :href="
                            route('reports.sales.pdf') +
                            (exportUrl ? '?' + exportUrl : '')
                        "
                        class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition"
                    >
                        PDF
                    </a>
                </div>
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
                                    Cari Kode Transaksi
                                </label>
                                <input
                                    id="search"
                                    v-model="search"
                                    type="text"
                                    placeholder="TRX-xxxxx"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
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
                            <div>
                                <label
                                    for="payment_method"
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                >
                                    Metode Pembayaran
                                </label>
                                <select
                                    id="payment_method"
                                    v-model="paymentMethodId"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">Semua Metode</option>
                                    <option
                                        v-for="pm in paymentMethods"
                                        :key="pm.id"
                                        :value="pm.id"
                                    >
                                        {{ pm.name }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label
                                    for="cashier"
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                >
                                    Kasir
                                </label>
                                <select
                                    id="cashier"
                                    v-model="userId"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">Semua Kasir</option>
                                    <option
                                        v-for="cashier in cashiers"
                                        :key="cashier.id"
                                        :value="cashier.id"
                                    >
                                        {{ cashier.name }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label
                                    for="payment_status"
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                >
                                    Status Pembayaran
                                </label>
                                <select
                                    id="payment_status"
                                    v-model="paymentStatus"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">Semua Status</option>
                                    <option value="paid">Lunas</option>
                                    <option value="pending">Pending</option>
                                </select>
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
                                        Kode
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Kasir
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Pembayaran
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Total
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Tanggal
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
                                    v-for="trx in transactions.data"
                                    :key="trx.id"
                                    class="hover:bg-gray-50 transition"
                                >
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                    >
                                        {{ trx.code }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"
                                    >
                                        {{ trx.cashier || "-" }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"
                                    >
                                        {{ trx.payment_method || "-" }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-center"
                                    >
                                        <span
                                            :class="[
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize',
                                                getStatusBadge(
                                                    trx.payment_status,
                                                ),
                                            ]"
                                        >
                                            {{
                                                trx.payment_status === "paid"
                                                    ? "Lunas"
                                                    : "Pending"
                                            }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900"
                                    >
                                        {{ formatCurrency(trx.total) }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"
                                    >
                                        {{ trx.created_at }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                                    >
                                        <Link
                                            :href="
                                                route('reports.show', trx.id)
                                            "
                                            class="text-indigo-600 hover:text-indigo-900"
                                        >
                                            Detail
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="transactions.data.length === 0">
                                    <td
                                        colspan="7"
                                        class="px-6 py-8 text-center text-gray-500"
                                    >
                                        Tidak ada transaksi ditemukan
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="transactions.links.length > 3"
                        class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6"
                    >
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Menampilkan
                                <span class="font-medium">{{
                                    transactions.from
                                }}</span>
                                sampai
                                <span class="font-medium">{{
                                    transactions.to
                                }}</span>
                                dari
                                <span class="font-medium">{{
                                    transactions.total
                                }}</span>
                                transaksi
                            </div>
                            <div class="flex gap-1">
                                <Link
                                    v-for="link in transactions.links"
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
