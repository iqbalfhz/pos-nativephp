<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { showError, showSuccess } from "@/lib/swal";

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    roles: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: "",
    password_confirmation: "",
    roles: props.user.roles,
});

const submit = () => {
    form.put(route("users.update", props.user.id), {
        onSuccess: () => {
            showSuccess("User berhasil diperbarui.");
        },
        onError: (errors) => {
            const firstError = Object.values(errors)[0];
            showError(firstError || "Gagal memperbarui user.");
        },
    });
};
</script>

<template>
    <Head title="Edit User" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit User
                </h2>
                <Link
                    :href="route('users.index')"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    Kembali
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Name -->
                        <div>
                            <label
                                for="name"
                                class="block text-sm font-medium text-gray-700 mb-1"
                            >
                                Nama Lengkap
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            />
                            <p
                                v-if="form.errors.name"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label
                                for="email"
                                class="block text-sm font-medium text-gray-700 mb-1"
                            >
                                Email
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            />
                            <p
                                v-if="form.errors.email"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <!-- Password (Optional) -->
                        <div
                            class="p-4 bg-yellow-50 border border-yellow-200 rounded-md"
                        >
                            <p class="text-sm text-yellow-800 mb-3">
                                <strong>Catatan:</strong> Kosongkan password
                                jika tidak ingin mengubahnya
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        for="password"
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                    >
                                        Password Baru
                                    </label>
                                    <input
                                        id="password"
                                        v-model="form.password"
                                        type="password"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    <p
                                        v-if="form.errors.password"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.password }}
                                    </p>
                                </div>
                                <div>
                                    <label
                                        for="password_confirmation"
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                    >
                                        Konfirmasi Password Baru
                                    </label>
                                    <input
                                        id="password_confirmation"
                                        v-model="form.password_confirmation"
                                        type="password"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Roles -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Role
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-2">
                                <div
                                    v-for="role in roles"
                                    :key="role.id"
                                    class="flex items-center"
                                >
                                    <input
                                        :id="`role-${role.id}`"
                                        v-model="form.roles"
                                        :value="role.id"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <label
                                        :for="`role-${role.id}`"
                                        class="ml-2 block text-sm text-gray-700"
                                    >
                                        {{ role.name }}
                                    </label>
                                </div>
                            </div>
                            <p
                                v-if="form.errors.roles"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.roles }}
                            </p>
                        </div>

                        <!-- Submit Buttons -->
                        <div
                            class="flex items-center justify-end gap-3 pt-4 border-t"
                        >
                            <Link
                                :href="route('users.index')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition"
                            >
                                Batal
                            </Link>
                            <button
                                type="submit"
                                class="px-4 py-2 text-sm font-semibold text-white bg-gray-800 rounded-md hover:bg-gray-700 transition"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing"
                                    >Memperbarui...</span
                                >
                                <span v-else>Update User</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
