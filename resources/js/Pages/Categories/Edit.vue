<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { showError, showSuccess } from "@/lib/swal";

const props = defineProps({
    category: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.category.name,
    description: props.category.description || "",
});

const submit = () => {
    form.put(route("categories.update", props.category.id), {
        onSuccess: () => {
            showSuccess("Kategori berhasil diperbarui.");
        },
        onError: (errors) => {
            const firstError = Object.values(errors)[0];
            showError(firstError || "Gagal memperbarui kategori.");
        },
    });
};
</script>

<template>
    <Head title="Edit Category" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Category
                </h2>
                <Link
                    :href="route('categories.index')"
                    class="text-sm text-gray-600"
                    >Back</Link
                >
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-4">
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
                                >Description</label
                            >
                            <textarea
                                v-model="form.description"
                                rows="3"
                                class="mt-1 w-full rounded-md border-gray-300"
                            ></textarea>
                            <p
                                v-if="form.errors.description"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.description }}
                            </p>
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
