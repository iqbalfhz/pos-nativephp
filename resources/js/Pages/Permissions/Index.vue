<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { confirmAction } from "@/lib/swal";

const props = defineProps({
    permissions: {
        type: Object,
        required: true,
    },
});

const deletePermission = (permission) => {
    confirmAction(
        "Delete Permission",
        `Are you sure you want to delete the permission "${permission.name}"?`,
        () => {
            router.delete(route("permissions.destroy", permission.id));
        },
    );
};
</script>

<template>
    <Head title="Permissions" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Permissions
                </h2>
                <Link
                    :href="route('permissions.create')"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                >
                    Add New Permission
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Name
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Description
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Assigned to Roles
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr
                                v-for="permission in permissions.data"
                                :key="permission.id"
                                class="hover:bg-gray-50"
                            >
                                <td
                                    class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900"
                                >
                                    {{ permission.name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ permission.description || "-" }}
                                </td>
                                <td
                                    class="whitespace-nowrap px-6 py-4 text-sm text-gray-500"
                                >
                                    {{ permission.roles_count }} roles
                                </td>
                                <td
                                    class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium"
                                >
                                    <Link
                                        :href="
                                            route(
                                                'permissions.edit',
                                                permission.id,
                                            )
                                        "
                                        class="mr-3 text-indigo-600 hover:text-indigo-900"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        @click="deletePermission(permission)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div
                        v-if="permissions.links.length > 3"
                        class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6"
                    >
                        <div class="flex flex-1 justify-between sm:hidden">
                            <Link
                                v-if="permissions.prev_page_url"
                                :href="permissions.prev_page_url"
                                class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Previous
                            </Link>
                            <Link
                                v-if="permissions.next_page_url"
                                :href="permissions.next_page_url"
                                class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Next
                            </Link>
                        </div>
                        <div
                            class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between"
                        >
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing
                                    <span class="font-medium">{{
                                        permissions.from
                                    }}</span>
                                    to
                                    <span class="font-medium">{{
                                        permissions.to
                                    }}</span>
                                    of
                                    <span class="font-medium">{{
                                        permissions.total
                                    }}</span>
                                    results
                                </p>
                            </div>
                            <div>
                                <nav
                                    class="isolate inline-flex -space-x-px rounded-md shadow-sm"
                                >
                                    <Link
                                        v-for="(
                                            link, index
                                        ) in permissions.links"
                                        :key="index"
                                        :href="link.url"
                                        :class="[
                                            link.active
                                                ? 'z-10 bg-indigo-600 text-white'
                                                : 'bg-white text-gray-500 hover:bg-gray-50',
                                            index === 0 ? 'rounded-l-md' : '',
                                            index ===
                                            permissions.links.length - 1
                                                ? 'rounded-r-md'
                                                : '',
                                            'relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300',
                                        ]"
                                        v-html="link.label"
                                    />
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
