<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    name: props.product.name,
    sku: props.product.sku,
    barcode: props.product.barcode || "",
    category_id: props.product.category_id || "",
    price: props.product.price,
    cost: props.product.cost,
    stock: props.product.stock,
    min_stock: props.product.min_stock,
    is_active: props.product.is_active,
});

const submit = () => {
    form.put(route("products.update", props.product.id));
};
</script>

<template>
    <Head title="Edit Product" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Product
                </h2>
                <Link
                    :href="route('products.index')"
                    class="text-sm text-gray-600"
                    >Back</Link
                >
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Name</label
                                >
                                <input
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 w-full rounded-md border-gray-300"
                                    required
                                />
                                <p
                                    v-if="form.errors.name"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.name }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >SKU</label
                                >
                                <input
                                    v-model="form.sku"
                                    type="text"
                                    class="mt-1 w-full rounded-md border-gray-300"
                                    required
                                />
                                <p
                                    v-if="form.errors.sku"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.sku }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Barcode</label
                                >
                                <input
                                    v-model="form.barcode"
                                    type="text"
                                    class="mt-1 w-full rounded-md border-gray-300"
                                />
                                <p
                                    v-if="form.errors.barcode"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.barcode }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Category</label
                                >
                                <select
                                    v-model="form.category_id"
                                    class="mt-1 w-full rounded-md border-gray-300"
                                >
                                    <option value="">-</option>
                                    <option
                                        v-for="category in props.categories"
                                        :key="category.id"
                                        :value="category.id"
                                    >
                                        {{ category.name }}
                                    </option>
                                </select>
                                <p
                                    v-if="form.errors.category_id"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.category_id }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Price</label
                                >
                                <input
                                    v-model="form.price"
                                    type="number"
                                    step="0.01"
                                    class="mt-1 w-full rounded-md border-gray-300"
                                    required
                                />
                                <p
                                    v-if="form.errors.price"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.price }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Cost</label
                                >
                                <input
                                    v-model="form.cost"
                                    type="number"
                                    step="0.01"
                                    class="mt-1 w-full rounded-md border-gray-300"
                                />
                                <p
                                    v-if="form.errors.cost"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.cost }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Stock</label
                                >
                                <input
                                    v-model="form.stock"
                                    type="number"
                                    class="mt-1 w-full rounded-md border-gray-300"
                                    required
                                />
                                <p
                                    v-if="form.errors.stock"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.stock }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Min Stock</label
                                >
                                <input
                                    v-model="form.min_stock"
                                    type="number"
                                    class="mt-1 w-full rounded-md border-gray-300"
                                />
                                <p
                                    v-if="form.errors.min_stock"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.min_stock }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <label
                                class="flex items-center gap-2 text-sm text-gray-700"
                            >
                                <input
                                    v-model="form.is_active"
                                    type="checkbox"
                                    class="rounded border-gray-300"
                                />
                                Active
                            </label>
                        </div>
                        <div class="flex justify-end">
                            <button
                                class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white"
                                :disabled="form.processing"
                            >
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
