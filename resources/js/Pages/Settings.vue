<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    settings: {
        type: Object,
        required: true,
    },
    defaults: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    store_name: props.settings.store_name || "",
    store_address: props.settings.store_address || "",
    store_phone: props.settings.store_phone || "",
    store_tax_id: props.settings.store_tax_id || "",
    pb1_rate: props.settings.pb1_rate || 10,
    receipt_header: props.settings.receipt_header || "",
    receipt_footer: props.settings.receipt_footer || "",
    low_stock_threshold: props.settings.low_stock_threshold || 10,
    enable_notifications: props.settings.enable_notifications !== false,
});

const submit = () => {
    form.put(route("settings.update"));
};

const reset = () => {
    if (confirm("Reset semua pengaturan ke nilai default?")) {
        useForm({}).post(route("settings.reset"));
    }
};
</script>

<template>
    <Head title="Pengaturan Sistem" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Pengaturan Sistem
                </h2>
                <button
                    type="button"
                    @click="reset"
                    class="rounded-md bg-gray-300 px-4 py-2 text-sm font-medium text-gray-800 hover:bg-gray-400 transition"
                >
                    Reset ke Default
                </button>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Store Information Section -->
                    <div class="bg-white shadow sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Informasi Toko
                        </h3>
                        <div class="space-y-4">
                            <!-- Store Name -->
                            <div>
                                <label
                                    for="store_name"
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                >
                                    Nama Toko
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="store_name"
                                    v-model="form.store_name"
                                    type="text"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                />
                                <p
                                    v-if="form.errors.store_name"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.store_name }}
                                </p>
                            </div>

                            <!-- Store Address -->
                            <div>
                                <label
                                    for="store_address"
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                >
                                    Alamat Toko
                                    <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    id="store_address"
                                    v-model="form.store_address"
                                    rows="3"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                ></textarea>
                                <p
                                    v-if="form.errors.store_address"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.store_address }}
                                </p>
                            </div>

                            <!-- Store Phone -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        for="store_phone"
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                    >
                                        Nomor Telepon
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="store_phone"
                                        v-model="form.store_phone"
                                        type="text"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    />
                                    <p
                                        v-if="form.errors.store_phone"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.store_phone }}
                                    </p>
                                </div>

                                <!-- Tax ID -->
                                <div>
                                    <label
                                        for="store_tax_id"
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                    >
                                        NPWP / Tax ID
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="store_tax_id"
                                        v-model="form.store_tax_id"
                                        type="text"
                                        placeholder="00.000.000.0-000.000"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    />
                                    <p
                                        v-if="form.errors.store_tax_id"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.store_tax_id }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tax & Receipt Section -->
                    <div class="bg-white shadow sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Pajak & Struk
                        </h3>
                        <div class="space-y-4">
                            <!-- PB1 Rate -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        for="pb1_rate"
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                    >
                                        Tarif PB1 (%)
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="pb1_rate"
                                        v-model.number="form.pb1_rate"
                                        type="number"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    />
                                    <p class="mt-1 text-sm text-gray-500">
                                        Pajak Barang & Jasa untuk F&B
                                    </p>
                                    <p
                                        v-if="form.errors.pb1_rate"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.pb1_rate }}
                                    </p>
                                </div>

                                <!-- Low Stock Threshold -->
                                <div>
                                    <label
                                        for="low_stock_threshold"
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                    >
                                        Minimal Stok Default
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="low_stock_threshold"
                                        v-model.number="
                                            form.low_stock_threshold
                                        "
                                        type="number"
                                        min="0"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    />
                                    <p class="mt-1 text-sm text-gray-500">
                                        Default minimal stok untuk produk baru
                                    </p>
                                    <p
                                        v-if="form.errors.low_stock_threshold"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.low_stock_threshold }}
                                    </p>
                                </div>
                            </div>

                            <!-- Receipt Header -->
                            <div>
                                <label
                                    for="receipt_header"
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                >
                                    Header Struk
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="receipt_header"
                                    v-model="form.receipt_header"
                                    type="text"
                                    placeholder="Terima Kasih Telah Berbelanja"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                />
                                <p
                                    v-if="form.errors.receipt_header"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.receipt_header }}
                                </p>
                            </div>

                            <!-- Receipt Footer -->
                            <div>
                                <label
                                    for="receipt_footer"
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                >
                                    Footer Struk
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="receipt_footer"
                                    v-model="form.receipt_footer"
                                    type="text"
                                    placeholder="Semoga Puas dengan Layanan Kami"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                />
                                <p
                                    v-if="form.errors.receipt_footer"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.receipt_footer }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications Section -->
                    <div class="bg-white shadow sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Notifikasi
                        </h3>
                        <div class="flex items-center">
                            <input
                                id="enable_notifications"
                                v-model="form.enable_notifications"
                                type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            <label
                                for="enable_notifications"
                                class="ml-2 block text-sm text-gray-700"
                            >
                                Aktifkan notifikasi sistem
                            </label>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Tampilkan notifikasi untuk stok rendah, transaksi
                            baru, dll
                        </p>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-3">
                        <Link
                            :href="route('dashboard')"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-md hover:bg-indigo-700 transition"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">Menyimpan...</span>
                            <span v-else>Simpan Pengaturan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
