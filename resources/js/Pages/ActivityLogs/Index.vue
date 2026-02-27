<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({
    logs: {
        type: Object,
        required: true,
    },
    users: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    methods: {
        type: Array,
        default: () => ["GET", "POST", "PUT", "PATCH", "DELETE"],
    },
});

const search = ref(props.filters.search || "");
const method = ref(props.filters.method || "");
const userId = ref(props.filters.user_id || "");
const startDate = ref(props.filters.start_date || "");
const endDate = ref(props.filters.end_date || "");

watch([search, method, userId, startDate, endDate], () => {
    router.get(
        route("activity-logs.index"),
        {
            search: search.value,
            method: method.value,
            user_id: userId.value,
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
    method.value = "";
    userId.value = "";
    startDate.value = "";
    endDate.value = "";
};

const methodBadgeClass = (value) => {
    const classes = {
        GET: "bg-blue-100 text-blue-800",
        POST: "bg-green-100 text-green-800",
        PUT: "bg-yellow-100 text-yellow-800",
        PATCH: "bg-amber-100 text-amber-800",
        DELETE: "bg-red-100 text-red-800",
    };

    return classes[value] || "bg-gray-100 text-gray-800";
};
</script>

<template>
    <Head title="Activity Logs" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Activity Logs
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                    <div class="border-b bg-gray-50 p-6">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-6">
                            <div class="md:col-span-2">
                                <label
                                    class="mb-1 block text-sm font-medium text-gray-700"
                                    >Cari</label
                                >
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="URL, route, atau user..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-sm font-medium text-gray-700"
                                    >Method</label
                                >
                                <select
                                    v-model="method"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">Semua</option>
                                    <option
                                        v-for="item in methods"
                                        :key="item"
                                        :value="item"
                                    >
                                        {{ item }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-sm font-medium text-gray-700"
                                    >User</label
                                >
                                <select
                                    v-model="userId"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">Semua</option>
                                    <option
                                        v-for="user in users"
                                        :key="user.id"
                                        :value="user.id"
                                    >
                                        {{ user.name }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-sm font-medium text-gray-700"
                                    >Dari</label
                                >
                                <input
                                    v-model="startDate"
                                    type="date"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-sm font-medium text-gray-700"
                                    >Sampai</label
                                >
                                <input
                                    v-model="endDate"
                                    type="date"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>
                        </div>
                        <div class="mt-4">
                            <button
                                type="button"
                                @click="resetFilters"
                                class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Reset Filter
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full divide-y divide-gray-200 text-sm"
                        >
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left font-medium text-gray-500"
                                    >
                                        Waktu
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left font-medium text-gray-500"
                                    >
                                        User
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left font-medium text-gray-500"
                                    >
                                        Method
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left font-medium text-gray-500"
                                    >
                                        Route
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left font-medium text-gray-500"
                                    >
                                        URL
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left font-medium text-gray-500"
                                    >
                                        IP
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left font-medium text-gray-500"
                                    >
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr
                                    v-for="log in logs.data"
                                    :key="log.id"
                                    class="align-top"
                                >
                                    <td
                                        class="px-4 py-3 text-gray-700 whitespace-nowrap"
                                    >
                                        {{ log.created_at }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-gray-700 whitespace-nowrap"
                                    >
                                        {{ log.user }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span
                                            class="rounded-full px-2 py-1 text-xs font-semibold"
                                            :class="
                                                methodBadgeClass(log.method)
                                            "
                                        >
                                            {{ log.method }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">
                                        {{ log.route_name || "-" }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-gray-700 break-all"
                                    >
                                        {{ log.url }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-gray-700 whitespace-nowrap"
                                    >
                                        {{ log.ip_address || "-" }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-gray-700 whitespace-nowrap"
                                    >
                                        {{ log.status_code || "-" }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        class="flex items-center justify-between border-t px-6 py-4 text-sm text-gray-600"
                    >
                        <span>
                            Menampilkan {{ logs.from || 0 }} sampai
                            {{ logs.to || 0 }} dari {{ logs.total }} aktivitas
                        </span>
                        <div class="flex gap-1">
                            <button
                                v-for="link in logs.links"
                                :key="link.label"
                                @click="link.url && router.visit(link.url)"
                                :disabled="!link.url"
                                class="rounded border px-3 py-1"
                                :class="[
                                    link.active
                                        ? 'border-indigo-600 bg-indigo-50 text-indigo-700'
                                        : 'border-gray-300 bg-white text-gray-700',
                                    !link.url
                                        ? 'cursor-not-allowed opacity-50'
                                        : 'hover:bg-gray-50',
                                ]"
                                v-html="link.label"
                            ></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
