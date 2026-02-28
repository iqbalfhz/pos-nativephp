<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { showSuccess, showError } from "@/lib/swal";
import { computed } from "vue";

const props = defineProps({
    role: {
        type: Object,
        required: true,
    },
    permissions: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    name: props.role.name,
    description: props.role.description,
    permissions: props.role.permission_ids,
});

// Group permissions by category
const groupedPermissions = computed(() => {
    const groups = {
        dashboard: { label: "Dashboard", permissions: [] },
        products: { label: "Products", permissions: [] },
        stock: { label: "Stock Management", permissions: [] },
        categories: { label: "Categories", permissions: [] },
        discounts: { label: "Discounts", permissions: [] },
        payments: { label: "Payment Methods", permissions: [] },
        sales: { label: "Sales / Cashier", permissions: [] },
        reports: { label: "Reports", permissions: [] },
        activityLogs: { label: "Activity Logs", permissions: [] },
        users: { label: "Users", permissions: [] },
        roles: { label: "Roles", permissions: [] },
        permissions: { label: "Permissions", permissions: [] },
        settings: { label: "Settings", permissions: [] },
    };

    props.permissions.forEach((permission) => {
        if (permission.name.includes("dashboard")) {
            groups.dashboard.permissions.push(permission);
        } else if (permission.name.includes("product")) {
            groups.products.permissions.push(permission);
        } else if (permission.name.includes("stock")) {
            groups.stock.permissions.push(permission);
        } else if (permission.name.includes("categor")) {
            groups.categories.permissions.push(permission);
        } else if (permission.name.includes("discount")) {
            groups.discounts.permissions.push(permission);
        } else if (permission.name.includes("payment")) {
            groups.payments.permissions.push(permission);
        } else if (permission.name.includes("sales")) {
            groups.sales.permissions.push(permission);
        } else if (permission.name.includes("report")) {
            groups.reports.permissions.push(permission);
        } else if (permission.name.includes("activity")) {
            groups.activityLogs.permissions.push(permission);
        } else if (permission.name.includes("user")) {
            groups.users.permissions.push(permission);
        } else if (permission.name.includes("role")) {
            groups.roles.permissions.push(permission);
        } else if (permission.name.includes("permission")) {
            groups.permissions.permissions.push(permission);
        } else if (permission.name.includes("setting")) {
            groups.settings.permissions.push(permission);
        }
    });

    // Filter out empty groups
    return Object.values(groups).filter(
        (group) => group.permissions.length > 0,
    );
});

const submit = () => {
    form.put(route("roles.update", props.role.id), {
        onSuccess: () => {
            showSuccess("Role updated successfully!");
        },
        onError: (errors) => {
            const firstError = Object.values(errors)[0];
            showError(firstError);
        },
    });
};
</script>

<template>
    <Head title="Edit Role" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Role
                </h2>
                <Link
                    :href="route('roles.index')"
                    class="rounded-md bg-gray-600 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700"
                >
                    Back to List
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <div>
                            <label
                                for="name"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Role Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                v-model="form.name"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required
                            />
                            <p
                                v-if="form.errors.name"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div>
                            <label
                                for="description"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Description
                            </label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            ></textarea>
                            <p
                                v-if="form.errors.description"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-3"
                            >
                                Permissions
                            </label>
                            <div
                                class="max-h-[500px] overflow-y-auto border border-gray-200 rounded-md p-4"
                            >
                                <div
                                    class="grid grid-cols-1 lg:grid-cols-2 gap-6"
                                >
                                    <div
                                        v-for="group in groupedPermissions"
                                        :key="group.label"
                                        class="bg-gray-50 rounded-lg p-4 border border-gray-200"
                                    >
                                        <h3
                                            class="font-semibold text-gray-900 mb-3 text-sm"
                                        >
                                            {{ group.label }}
                                        </h3>
                                        <div class="space-y-2.5">
                                            <div
                                                v-for="permission in group.permissions"
                                                :key="permission.id"
                                                class="flex items-start"
                                            >
                                                <div
                                                    class="flex h-5 items-center"
                                                >
                                                    <input
                                                        :id="`permission-${permission.id}`"
                                                        v-model="
                                                            form.permissions
                                                        "
                                                        :value="permission.id"
                                                        type="checkbox"
                                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                    />
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label
                                                        :for="`permission-${permission.id}`"
                                                        class="font-medium text-gray-700 cursor-pointer"
                                                    >
                                                        {{ permission.name }}
                                                    </label>
                                                    <p
                                                        v-if="
                                                            permission.description
                                                        "
                                                        class="text-gray-500 text-xs mt-0.5"
                                                    >
                                                        {{
                                                            permission.description
                                                        }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p
                                v-if="form.errors.permissions"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.permissions }}
                            </p>
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <Link
                                :href="route('roles.index')"
                                class="rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 disabled:opacity-50"
                            >
                                Update Role
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
