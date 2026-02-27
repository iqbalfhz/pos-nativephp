<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";
import { confirmAction, showError } from "@/lib/swal";

const props = defineProps({
    products: {
        type: Array,
        default: () => [],
    },
    paymentMethods: {
        type: Array,
        default: () => [],
    },
    defaultPaymentMethodId: {
        type: [Number, String],
        default: null,
    },
});

const query = ref("");
const cart = ref([]);
const barcodeInput = ref("");
const discountType = ref("none"); // none, percentage, nominal
const discountValue = ref(0);
const taxRate = ref(10); // PB1 10%
const includeTax = ref(false);
const cashAmount = ref(0);

const form = useForm({
    items: [],
    payment_method_id: "",
    customer_phone: "",
    notes: "",
    discount_total: 0,
    tax_total: 0,
});

// Set default payment method ke Cash ketika component mount
watch(
    () => props.defaultPaymentMethodId,
    (defaultId) => {
        if (defaultId && !form.payment_method_id) {
            form.payment_method_id = String(defaultId);
        }
    },
    { immediate: true },
);

const filteredProducts = computed(() => {
    const term = query.value.toLowerCase();
    return props.products.filter(
        (product) =>
            product.name.toLowerCase().includes(term) ||
            (product.sku || "").toLowerCase().includes(term) ||
            (product.barcode || "").toLowerCase().includes(term),
    );
});

const scanBarcode = () => {
    if (!barcodeInput.value) return;

    const product = props.products.find(
        (p) => p.barcode === barcodeInput.value || p.sku === barcodeInput.value,
    );

    if (!product) {
        showError("Produk tidak ditemukan!");
        barcodeInput.value = "";
        return;
    }

    if (product.stock === 0) {
        showError("Produk habis! Tidak bisa ditambahkan ke keranjang.");
        barcodeInput.value = "";
        return;
    }

    addToCart(product);
    barcodeInput.value = "";
};

const addToCart = (product) => {
    // Validasi stok sebelum menambah ke cart
    if (product.stock === 0) {
        showError("Produk habis! Tidak bisa ditambahkan ke keranjang.");
        return;
    }

    const existing = cart.value.find((item) => item.product_id === product.id);
    if (existing) {
        if (existing.qty >= existing.stock) {
            showError(`Stok maksimal untuk produk ini: ${existing.stock}`);
            return;
        }
        existing.qty += 1;
    } else {
        cart.value.push({
            product_id: product.id,
            name: product.name,
            price: Number(product.price),
            qty: 1,
            stock: product.stock,
        });
    }
};

const updateQty = (productId, newQty) => {
    const item = cart.value.find((i) => i.product_id === productId);
    if (item) {
        if (newQty <= 0) {
            removeFromCart(productId);
        } else if (newQty <= item.stock) {
            item.qty = newQty;
        } else {
            showError(`Stok tidak mencukupi. Stok tersedia: ${item.stock}`);
        }
    }
};

const incrementQty = (productId) => {
    const item = cart.value.find((i) => i.product_id === productId);
    if (item) {
        if (item.qty < item.stock) {
            item.qty += 1;
        } else {
            showError(`Stok maksimal: ${item.stock}`);
        }
    }
};

const decrementQty = (productId) => {
    const item = cart.value.find((i) => i.product_id === productId);
    if (item) {
        if (item.qty > 1) {
            item.qty -= 1;
        } else {
            removeFromCart(productId);
        }
    }
};

const removeFromCart = (productId) => {
    cart.value = cart.value.filter((item) => item.product_id !== productId);
};

const subtotal = computed(() =>
    cart.value.reduce((sum, item) => sum + item.price * item.qty, 0),
);

const discountAmount = computed(() => {
    if (discountType.value === "percentage") {
        return (subtotal.value * discountValue.value) / 100;
    } else if (discountType.value === "nominal") {
        return Math.min(discountValue.value, subtotal.value);
    }
    return 0;
});

const subtotalAfterDiscount = computed(
    () => subtotal.value - discountAmount.value,
);

const taxAmount = computed(() =>
    includeTax.value ? (subtotalAfterDiscount.value * taxRate.value) / 100 : 0,
);

const grandTotal = computed(
    () => subtotalAfterDiscount.value + taxAmount.value,
);

const changeAmount = computed(() => {
    const change = cashAmount.value - grandTotal.value;
    return change >= 0 ? change : 0;
});

const isPaymentSufficient = computed(
    () => cashAmount.value >= grandTotal.value,
);

const selectedPaymentMethod = computed(() => {
    if (!form.payment_method_id) return null;
    return props.paymentMethods.find(
        (m) => m.id === parseInt(form.payment_method_id),
    );
});

const isCashPayment = computed(() => {
    if (!selectedPaymentMethod.value) return true; // Default: show cash input jika belum pilih
    return selectedPaymentMethod.value.name.toLowerCase() === "cash";
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
};

const totalItems = computed(() =>
    cart.value.reduce((sum, item) => sum + item.qty, 0),
);

const clearCart = async () => {
    const result = await confirmAction({
        title: "Kosongkan keranjang?",
        text: "Semua item di keranjang akan dihapus.",
        confirmButtonText: "Ya, kosongkan",
    });

    if (!result.isConfirmed) {
        return;
    }

    cart.value = [];
    discountType.value = "none";
    discountValue.value = 0;
    includeTax.value = false;
    cashAmount.value = 0;
};

const checkout = () => {
    // Validasi pembayaran hanya untuk Cash
    if (isCashPayment.value && !isPaymentSufficient.value) {
        showError("Jumlah uang yang dibayarkan tidak mencukupi!");
        return;
    }

    form.items = cart.value.map((item) => ({
        product_id: item.product_id,
        qty: item.qty,
    }));

    form.discount_total = discountAmount.value;
    form.tax_total = taxAmount.value;

    form.post(route("cashier.checkout"), {
        onSuccess: () => {
            cart.value = [];
            discountType.value = "none";
            discountValue.value = 0;
            includeTax.value = false;
            cashAmount.value = 0;
            form.reset("items", "notes", "discount_total", "tax_total");
        },
    });
};
</script>

<template>
    <Head title="Cashier" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Cashier
            </h2>
        </template>

        <div class="py-8">
            <div
                class="mx-auto max-w-6xl sm:px-6 lg:px-8 grid gap-6 lg:grid-cols-3"
            >
                <div class="lg:col-span-2 space-y-4">
                    <div class="bg-white shadow sm:rounded-lg p-4 space-y-3">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                            >
                                Scan Barcode / SKU
                            </label>
                            <div class="flex gap-2">
                                <input
                                    v-model="barcodeInput"
                                    @keyup.enter="scanBarcode"
                                    type="text"
                                    placeholder="Scan atau ketik barcode/SKU"
                                    class="flex-1 rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                />
                                <button
                                    @click="scanBarcode"
                                    type="button"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                                >
                                    <svg
                                        class="w-5 h-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 4v16m8-8H4"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                            >
                                Cari Produk
                            </label>
                            <input
                                v-model="query"
                                type="text"
                                placeholder="Cari nama produk, SKU, atau barcode"
                                class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>
                    </div>
                    <div class="bg-white shadow sm:rounded-lg p-4">
                        <div
                            v-if="filteredProducts.length === 0"
                            class="text-center py-8 text-gray-500"
                        >
                            <p>Tidak ada produk ditemukan</p>
                        </div>
                        <div v-else class="grid gap-3 sm:grid-cols-2">
                            <button
                                v-for="product in filteredProducts"
                                :key="product.id"
                                type="button"
                                :disabled="product.stock === 0"
                                :class="[
                                    'flex items-center justify-between rounded-md border px-3 py-2 text-left transition',
                                    product.stock === 0
                                        ? 'opacity-50 cursor-not-allowed bg-gray-100'
                                        : 'hover:bg-blue-50 hover:border-blue-400',
                                ]"
                                @click="addToCart(product)"
                            >
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">
                                        {{ product.name }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ product.sku }}
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span
                                            :class="[
                                                'text-xs px-2 py-0.5 rounded-full',
                                                product.stock === 0
                                                    ? 'bg-red-100 text-red-700'
                                                    : product.stock <= 10
                                                      ? 'bg-yellow-100 text-yellow-700'
                                                      : 'bg-green-100 text-green-700',
                                            ]"
                                        >
                                            Stok: {{ product.stock }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right ml-3">
                                    <div
                                        class="text-sm font-semibold text-blue-600"
                                    >
                                        {{ formatCurrency(product.price) }}
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow sm:rounded-lg p-4 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold">
                            Cart
                            <span
                                v-if="cart.length"
                                class="text-sm text-gray-500 font-normal"
                            >
                                ({{ totalItems }} items)
                            </span>
                        </h3>
                        <button
                            v-if="cart.length"
                            @click="clearCart"
                            type="button"
                            class="text-sm text-red-600 hover:text-red-800"
                        >
                            Clear All
                        </button>
                    </div>
                    <div
                        v-if="!cart.length"
                        class="text-center py-8 text-gray-500"
                    >
                        <svg
                            class="mx-auto h-12 w-12 text-gray-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                            />
                        </svg>
                        <p class="mt-2">Keranjang kosong</p>
                    </div>

                    <div v-else class="space-y-3 max-h-96 overflow-y-auto">
                        <div
                            v-for="item in cart"
                            :key="item.product_id"
                            class="border rounded-lg p-3 space-y-2"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">
                                        {{ item.name }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        {{ formatCurrency(item.price) }}
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    class="text-red-500 hover:text-red-700"
                                    @click="removeFromCart(item.product_id)"
                                >
                                    <svg
                                        class="w-5 h-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                        />
                                    </svg>
                                </button>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <button
                                        type="button"
                                        @click="decrementQty(item.product_id)"
                                        class="w-8 h-8 rounded-md border border-gray-300 flex items-center justify-center hover:bg-gray-100"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M20 12H4"
                                            />
                                        </svg>
                                    </button>
                                    <input
                                        type="number"
                                        :value="item.qty"
                                        @input="
                                            updateQty(
                                                item.product_id,
                                                parseInt($event.target.value) ||
                                                    0,
                                            )
                                        "
                                        class="w-16 text-center border-gray-300 rounded-md"
                                        min="1"
                                        :max="item.stock"
                                    />
                                    <button
                                        type="button"
                                        @click="incrementQty(item.product_id)"
                                        class="w-8 h-8 rounded-md border border-gray-300 flex items-center justify-center hover:bg-gray-100"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 4v16m8-8H4"
                                            />
                                        </svg>
                                    </button>
                                </div>
                                <div class="font-semibold text-gray-900">
                                    {{ formatCurrency(item.price * item.qty) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t pt-3 space-y-3">
                        <!-- Subtotal -->
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-semibold">{{
                                formatCurrency(subtotal)
                            }}</span>
                        </div>

                        <!-- Diskon -->
                        <div class="space-y-2">
                            <label
                                class="block text-sm font-medium text-gray-700"
                            >
                                Diskon
                            </label>
                            <div class="flex gap-2">
                                <select
                                    v-model="discountType"
                                    class="rounded-md border-gray-300 text-sm"
                                >
                                    <option value="none">Tanpa Diskon</option>
                                    <option value="percentage">
                                        Persentase (%)
                                    </option>
                                    <option value="nominal">
                                        Nominal (Rp)
                                    </option>
                                </select>
                                <input
                                    v-if="discountType !== 'none'"
                                    v-model.number="discountValue"
                                    type="number"
                                    min="0"
                                    :max="
                                        discountType === 'percentage'
                                            ? 100
                                            : subtotal
                                    "
                                    class="flex-1 rounded-md border-gray-300 text-sm"
                                    :placeholder="
                                        discountType === 'percentage'
                                            ? '0-100'
                                            : '0'
                                    "
                                />
                            </div>
                            <div
                                v-if="discountAmount > 0"
                                class="flex justify-between text-sm"
                            >
                                <span class="text-gray-600"
                                    >Potongan Diskon</span
                                >
                                <span class="text-red-600 font-semibold">
                                    -{{ formatCurrency(discountAmount) }}
                                </span>
                            </div>
                        </div>

                        <!-- PB1 -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label
                                    class="flex items-center space-x-2 text-sm font-medium text-gray-700"
                                >
                                    <input
                                        v-model="includeTax"
                                        type="checkbox"
                                        class="rounded border-gray-300"
                                    />
                                    <span>PB1 ({{ taxRate }}%)</span>
                                </label>
                            </div>
                            <div
                                v-if="includeTax && taxAmount > 0"
                                class="flex justify-between text-sm"
                            >
                                <span class="text-gray-600"
                                    >PB1 {{ taxRate }}%</span
                                >
                                <span class="font-semibold">
                                    +{{ formatCurrency(taxAmount) }}
                                </span>
                            </div>
                        </div>

                        <!-- Grand Total -->
                        <div
                            class="flex items-center justify-between text-lg font-bold border-t pt-2"
                        >
                            <span>Total Bayar</span>
                            <span class="text-blue-600">{{
                                formatCurrency(grandTotal)
                            }}</span>
                        </div>
                    </div>

                    <!-- Pembayaran -->
                    <div
                        v-if="cart.length && isCashPayment"
                        class="border-t pt-3 space-y-3"
                    >
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                            >
                                Jumlah Uang Dibayar
                            </label>
                            <input
                                v-model.number="cashAmount"
                                type="number"
                                min="0"
                                :placeholder="formatCurrency(grandTotal)"
                                class="w-full rounded-md border-gray-300 text-lg font-semibold"
                                @focus="$event.target.select()"
                            />
                        </div>

                        <!-- Quick Amount Buttons -->
                        <div class="grid grid-cols-3 gap-2">
                            <button
                                v-for="amount in [20000, 50000, 100000]"
                                :key="amount"
                                @click="cashAmount = amount"
                                type="button"
                                class="px-3 py-2 text-xs border rounded-md hover:bg-blue-50 hover:border-blue-400 transition"
                            >
                                {{ formatCurrency(amount) }}
                            </button>
                        </div>
                        <button
                            @click="
                                cashAmount = Math.ceil(grandTotal / 1000) * 1000
                            "
                            type="button"
                            class="w-full px-3 py-2 text-sm border border-blue-400 rounded-md hover:bg-blue-50 text-blue-600 font-medium transition"
                        >
                            💰 Uang Pas ({{
                                formatCurrency(
                                    Math.ceil(grandTotal / 1000) * 1000,
                                )
                            }})
                        </button>

                        <!-- Kembalian -->
                        <div
                            v-if="cashAmount > 0"
                            :class="[
                                'flex items-center justify-between p-3 rounded-lg',
                                isPaymentSufficient
                                    ? 'bg-green-50 border border-green-200'
                                    : 'bg-red-50 border border-red-200',
                            ]"
                        >
                            <span
                                class="font-medium"
                                :class="
                                    isPaymentSufficient
                                        ? 'text-green-700'
                                        : 'text-red-700'
                                "
                            >
                                Kembalian
                            </span>
                            <span
                                class="text-lg font-bold"
                                :class="
                                    isPaymentSufficient
                                        ? 'text-green-700'
                                        : 'text-red-700'
                                "
                            >
                                {{
                                    isPaymentSufficient
                                        ? formatCurrency(changeAmount)
                                        : "Kurang Bayar!"
                                }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Payment</label
                        >
                        <select
                            v-model="form.payment_method_id"
                            class="mt-1 w-full rounded-md border-gray-300"
                        >
                            <option
                                v-for="method in paymentMethods"
                                :key="method.id"
                                :value="method.id"
                            >
                                {{ method.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Customer Phone (optional)</label
                        >
                        <input
                            v-model="form.customer_phone"
                            type="text"
                            class="mt-1 w-full rounded-md border-gray-300"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Notes</label
                        >
                        <textarea
                            v-model="form.notes"
                            rows="2"
                            class="mt-1 w-full rounded-md border-gray-300"
                        ></textarea>
                    </div>

                    <button
                        type="button"
                        class="w-full rounded-md bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition"
                        :disabled="
                            form.processing ||
                            !cart.length ||
                            (isCashPayment && !isPaymentSufficient)
                        "
                        @click="checkout"
                    >
                        <span v-if="form.processing">Processing...</span>
                        <span v-else>
                            Bayar {{ formatCurrency(grandTotal) }}
                        </span>
                    </button>
                    <p v-if="form.errors.items" class="text-sm text-red-600">
                        {{ form.errors.items }}
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
