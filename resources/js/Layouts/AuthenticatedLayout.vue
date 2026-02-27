<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";

const showingNavigationDropdown = ref(false);

const linkClasses = (active) =>
    active
        ? "block rounded-md bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-700"
        : "block rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100";
</script>

<template>
    <div class="min-h-screen bg-gray-100 md:flex">
        <aside
            class="hidden md:flex md:w-64 md:flex-col border-r border-gray-200 bg-white"
        >
            <div class="flex h-16 items-center border-b border-gray-200 px-6">
                <Link :href="route('dashboard')" class="flex items-center">
                    <ApplicationLogo
                        class="block h-9 w-auto fill-current text-gray-800"
                    />
                </Link>
            </div>

            <nav class="flex-1 space-y-1 px-4 py-6">
                <Link
                    :href="route('dashboard')"
                    :class="linkClasses(route().current('dashboard'))"
                    >Dashboard</Link
                >
                <Link
                    :href="route('cashier.index')"
                    :class="linkClasses(route().current('cashier.*'))"
                    >Cashier</Link
                >
                <Link
                    :href="route('products.index')"
                    :class="linkClasses(route().current('products.*'))"
                    >Products</Link
                >
                <Link
                    :href="route('categories.index')"
                    :class="linkClasses(route().current('categories.*'))"
                    >Categories</Link
                >
                <Link
                    :href="route('stock.index')"
                    :class="linkClasses(route().current('stock.*'))"
                    >Stock</Link
                >
                <Link
                    :href="route('reports.sales')"
                    :class="linkClasses(route().current('reports.*'))"
                    >Reports</Link
                >
                <Link
                    :href="route('users.index')"
                    :class="linkClasses(route().current('users.*'))"
                    >Users</Link
                >
                <Link
                    :href="route('settings.index')"
                    :class="linkClasses(route().current('settings.*'))"
                    >Settings</Link
                >
                <Link
                    :href="route('activity-logs.index')"
                    :class="linkClasses(route().current('activity-logs.*'))"
                    >Activity Logs</Link
                >
            </nav>

            <div class="border-t border-gray-200 p-4">
                <div class="text-sm font-semibold text-gray-900">
                    {{ $page.props.auth.user.name }}
                </div>
                <div class="text-xs text-gray-500">
                    {{ $page.props.auth.user.email }}
                </div>
                <div class="mt-3 space-y-1">
                    <Link
                        :href="route('profile.edit')"
                        class="block rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100"
                        >Profile</Link
                    >
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="w-full rounded-md px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50"
                        >Log Out</Link
                    >
                </div>
            </div>
        </aside>

        <div class="flex-1 min-w-0">
            <div class="border-b border-gray-200 bg-white md:hidden">
                <div class="flex h-16 items-center justify-between px-4">
                    <Link :href="route('dashboard')" class="flex items-center">
                        <ApplicationLogo
                            class="block h-8 w-auto fill-current text-gray-800"
                        />
                    </Link>
                    <button
                        @click="
                            showingNavigationDropdown =
                                !showingNavigationDropdown
                        "
                        class="rounded-md p-2 text-gray-600 hover:bg-gray-100"
                    >
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                    </button>
                </div>

                <div
                    v-show="showingNavigationDropdown"
                    class="space-y-1 border-t border-gray-200 px-4 py-3"
                >
                    <Link
                        :href="route('dashboard')"
                        :class="linkClasses(route().current('dashboard'))"
                        >Dashboard</Link
                    >
                    <Link
                        :href="route('cashier.index')"
                        :class="linkClasses(route().current('cashier.*'))"
                        >Cashier</Link
                    >
                    <Link
                        :href="route('products.index')"
                        :class="linkClasses(route().current('products.*'))"
                        >Products</Link
                    >
                    <Link
                        :href="route('categories.index')"
                        :class="linkClasses(route().current('categories.*'))"
                        >Categories</Link
                    >
                    <Link
                        :href="route('stock.index')"
                        :class="linkClasses(route().current('stock.*'))"
                        >Stock</Link
                    >
                    <Link
                        :href="route('reports.sales')"
                        :class="linkClasses(route().current('reports.*'))"
                        >Reports</Link
                    >
                    <Link
                        :href="route('users.index')"
                        :class="linkClasses(route().current('users.*'))"
                        >Users</Link
                    >
                    <Link
                        :href="route('settings.index')"
                        :class="linkClasses(route().current('settings.*'))"
                        >Settings</Link
                    >
                    <Link
                        :href="route('activity-logs.index')"
                        :class="linkClasses(route().current('activity-logs.*'))"
                        >Activity Logs</Link
                    >
                    <Link
                        :href="route('profile.edit')"
                        class="block rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                        >Profile</Link
                    >
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="w-full rounded-md px-3 py-2 text-left text-sm font-medium text-red-600 hover:bg-red-50"
                        >Log Out</Link
                    >
                </div>
            </div>

            <header class="bg-white shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
