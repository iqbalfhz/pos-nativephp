<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({});

const removeCategory = (id) => {
    if (confirm("Hapus kategori ini?")) {
        form.delete(route("categories.destroy", id));
    }
};
</script>

<template>
    <Head title="Categories" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Categories
                </h2>
                <Link
                    :href="route('categories.create')"
                    class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white"
                >
                    Add Category
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                    <div class="p-6">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500">
                                    <th class="pb-3">Name</th>
                                    <th class="pb-3">Slug</th>
                                    <th class="pb-3">Description</th>
                                    <th class="pb-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="category in categories"
                                    :key="category.id"
                                    class="border-t"
                                >
                                    <td class="py-3 font-medium text-gray-900">
                                        {{ category.name }}
                                    </td>
                                    <td class="py-3 text-gray-600">
                                        {{ category.slug }}
                                    </td>
                                    <td class="py-3 text-gray-600">
                                        {{ category.description || "-" }}
                                    </td>
                                    <td class="py-3 text-right space-x-2">
                                        <Link
                                            :href="
                                                route(
                                                    'categories.edit',
                                                    category.id,
                                                )
                                            "
                                            class="text-indigo-600 hover:text-indigo-800"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            class="text-red-600 hover:text-red-800"
                                            type="button"
                                            @click="removeCategory(category.id)"
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p v-if="!categories.length" class="text-gray-500">
                            Belum ada kategori.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
