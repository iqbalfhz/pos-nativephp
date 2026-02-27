<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    products: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({});

const removeProduct = (id) => {
    if (confirm("Hapus produk ini?")) {
        form.delete(route("products.destroy", id));
    }
};
</script>

<template>
    <Head title="Products" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Products
                </h2>
                <Link
                    :href="route('products.create')"
                    class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white"
                >
                    Add Product
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                    <div class="p-6">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500">
                                    <th class="pb-3">Name</th>
                                    <th class="pb-3">SKU</th>
                                    <th class="pb-3">Category</th>
                                    <th class="pb-3">Price</th>
                                    <th class="pb-3">Stock</th>
                                    <th class="pb-3">Status</th>
                                    <th class="pb-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="product in products"
                                    :key="product.id"
                                    class="border-t"
                                >
                                    <td class="py-3 font-medium text-gray-900">
                                        {{ product.name }}
                                    </td>
                                    <td class="py-3 text-gray-600">
                                        {{ product.sku }}
                                    </td>
                                    <td class="py-3 text-gray-600">
                                        {{ product.category?.name || "-" }}
                                    </td>
                                    <td class="py-3 text-gray-600">
                                        {{ product.price }}
                                    </td>
                                    <td class="py-3 text-gray-600">
                                        {{ product.stock }}
                                    </td>
                                    <td class="py-3 text-gray-600">
                                        {{
                                            product.is_active
                                                ? "Active"
                                                : "Inactive"
                                        }}
                                    </td>
                                    <td class="py-3 text-right space-x-2">
                                        <Link
                                            :href="
                                                route(
                                                    'products.edit',
                                                    product.id,
                                                )
                                            "
                                            class="text-indigo-600 hover:text-indigo-800"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            class="text-red-600 hover:text-red-800"
                                            type="button"
                                            @click="removeProduct(product.id)"
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p v-if="!products.length" class="text-gray-500">
                            Belum ada produk.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
