<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

defineProps({
    transactions: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <Head title="Sales Report" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Sales Report
                </h2>
                <div class="space-x-2">
                    <a
                        :href="route('reports.sales.excel')"
                        class="rounded-md border px-3 py-2 text-sm text-gray-700"
                    >
                        Download Excel
                    </a>
                    <a
                        :href="route('reports.sales.pdf')"
                        class="rounded-md border px-3 py-2 text-sm text-gray-700"
                    >
                        Download PDF
                    </a>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                    <div class="p-6">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500">
                                    <th class="pb-3">Code</th>
                                    <th class="pb-3">Cashier</th>
                                    <th class="pb-3">Payment</th>
                                    <th class="pb-3">Status</th>
                                    <th class="pb-3">Total</th>
                                    <th class="pb-3">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="trx in transactions"
                                    :key="trx.id"
                                    class="border-t"
                                >
                                    <td class="py-3 font-medium text-gray-900">
                                        {{ trx.code }}
                                    </td>
                                    <td class="py-3 text-gray-600">
                                        {{ trx.cashier || "-" }}
                                    </td>
                                    <td class="py-3 text-gray-600">
                                        {{ trx.payment_method || "-" }}
                                    </td>
                                    <td class="py-3 text-gray-600">
                                        {{ trx.payment_status }}
                                    </td>
                                    <td class="py-3 text-gray-600">
                                        {{ trx.total }}
                                    </td>
                                    <td class="py-3 text-gray-600">
                                        {{ trx.created_at }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p v-if="!transactions.length" class="text-gray-500">
                            Belum ada transaksi.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
