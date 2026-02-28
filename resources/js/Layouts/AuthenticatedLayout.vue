<script setup>
import { ref, computed } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link, usePage } from "@inertiajs/vue3";

const showingNavigationDropdown = ref(false);
const page = usePage();

const permissions = computed(() => page.props.auth.permissions || []);

const hasGroupAccess = (permissionsArray) => {
    return permissionsArray.some((permission) => hasPermission(permission));
};
const hasPermission = (permission) => {
    return permissions.value.includes(permission);
};

// Permission group checks for sidebar menu
const canAccessProducts = computed(() =>
    hasGroupAccess([
        "view-products",
        "create-products",
        "edit-products",
        "delete-products",
    ]),
);
const canAccessCategories = computed(() =>
    hasGroupAccess([
        "view-categories",
        "create-categories",
        "edit-categories",
        "delete-categories",
    ]),
);
const canAccessStock = computed(() =>
    hasGroupAccess(["view-stock", "adjust-stock"]),
);
const canAccessDiscounts = computed(() =>
    hasGroupAccess([
        "view-discounts",
        "create-discounts",
        "edit-discounts",
        "delete-discounts",
    ]),
);
const canAccessPayments = computed(() =>
    hasGroupAccess([
        "view-payments",
        "create-payments",
        "edit-payments",
        "delete-payments",
    ]),
);
const canAccessUsers = computed(() =>
    hasGroupAccess([
        "view-users",
        "create-users",
        "edit-users",
        "delete-users",
    ]),
);
const canAccessRoles = computed(() =>
    hasGroupAccess([
        "view-roles",
        "create-roles",
        "edit-roles",
        "delete-roles",
    ]),
);
const canAccessPermissions = computed(() =>
    hasGroupAccess([
        "view-permissions",
        "create-permissions",
        "edit-permissions",
        "delete-permissions",
    ]),
);
const canAccessSettings = computed(() =>
    hasGroupAccess(["view-settings", "edit-settings"]),
);

const linkClasses = (active) =>
    active
        ? "flex items-center gap-3 rounded-md bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-700"
        : "flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100";
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

            <nav class="flex-1 overflow-y-auto px-4 py-6">
                <!-- Main -->
                <div class="space-y-1">
                    <Link
                        :href="route('dashboard')"
                        :class="linkClasses(route().current('dashboard'))"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                            />
                        </svg>
                        <span>Dashboard</span>
                    </Link>
                    <Link
                        v-if="hasPermission('process-sales')"
                        :href="route('cashier.index')"
                        :class="linkClasses(route().current('cashier.*'))"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                            />
                        </svg>
                        <span>Cashier</span>
                    </Link>
                </div>

                <!-- Inventory Management -->
                <div class="mt-6">
                    <div class="px-3 mb-2">
                        <p
                            class="text-xs font-semibold text-gray-400 uppercase tracking-wider"
                        >
                            Inventory
                        </p>
                    </div>
                    <div class="space-y-1">
                        <Link
                            v-if="canAccessProducts"
                            :href="route('products.index')"
                            :class="linkClasses(route().current('products.*'))"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                />
                            </svg>
                            <span>Products</span>
                        </Link>
                        <Link
                            v-if="canAccessCategories"
                            :href="route('categories.index')"
                            :class="
                                linkClasses(route().current('categories.*'))
                            "
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                />
                            </svg>
                            <span>Categories</span>
                        </Link>
                        <Link
                            v-if="canAccessStock"
                            :href="route('stock.index')"
                            :class="linkClasses(route().current('stock.*'))"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"
                                />
                            </svg>
                            <span>Stock</span>
                        </Link>
                    </div>
                </div>

                <!-- Reports & Logs -->
                <div class="mt-6">
                    <div class="px-3 mb-2">
                        <p
                            class="text-xs font-semibold text-gray-400 uppercase tracking-wider"
                        >
                            Reports
                        </p>
                    </div>
                    <div class="space-y-1">
                        <Link
                            v-if="hasPermission('view-reports')"
                            :href="route('reports.sales')"
                            :class="linkClasses(route().current('reports.*'))"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />
                            </svg>
                            <span>Reports</span>
                        </Link>
                        <Link
                            v-if="hasPermission('view-reports')"
                            :href="route('activity-logs.index')"
                            :class="
                                linkClasses(route().current('activity-logs.*'))
                            "
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                                />
                            </svg>
                            <span>Activity Logs</span>
                        </Link>
                    </div>
                </div>

                <!-- Administration -->
                <div class="mt-6">
                    <div class="px-3 mb-2">
                        <p
                            class="text-xs font-semibold text-gray-400 uppercase tracking-wider"
                        >
                            Administration
                        </p>
                    </div>
                    <div class="space-y-1">
                        <Link
                            v-if="canAccessUsers"
                            :href="route('users.index')"
                            :class="linkClasses(route().current('users.*'))"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                />
                            </svg>
                            <span>Users</span>
                        </Link>
                        <Link
                            v-if="canAccessRoles"
                            :href="route('roles.index')"
                            :class="linkClasses(route().current('roles.*'))"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                />
                            </svg>
                            <span>Roles</span>
                        </Link>
                        <Link
                            v-if="canAccessPermissions"
                            :href="route('permissions.index')"
                            :class="
                                linkClasses(route().current('permissions.*'))
                            "
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"
                                />
                            </svg>
                            <span>Permissions</span>
                        </Link>
                        <Link
                            v-if="canAccessSettings"
                            :href="route('settings.index')"
                            :class="linkClasses(route().current('settings.*'))"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                />
                            </svg>
                            <span>Settings</span>
                        </Link>
                    </div>
                </div>
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
                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                            />
                        </svg>
                        <span>Profile</span>
                    </Link>
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                            />
                        </svg>
                        <span>Log Out</span>
                    </Link>
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
                    class="max-h-[calc(100vh-4rem)] overflow-y-auto border-t border-gray-200 px-4 py-3"
                >
                    <!-- Main -->
                    <div class="space-y-1">
                        <Link
                            :href="route('dashboard')"
                            :class="linkClasses(route().current('dashboard'))"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                                />
                            </svg>
                            <span>Dashboard</span>
                        </Link>
                        <Link
                            v-if="hasPermission('process-sales')"
                            :href="route('cashier.index')"
                            :class="linkClasses(route().current('cashier.*'))"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                                />
                            </svg>
                            <span>Cashier</span>
                        </Link>
                    </div>

                    <!-- Inventory Management -->
                    <div class="mt-6">
                        <div class="px-3 mb-2">
                            <p
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider"
                            >
                                Inventory
                            </p>
                        </div>
                        <div class="space-y-1">
                            <Link
                                v-if="canAccessProducts"
                                :href="route('products.index')"
                                :class="
                                    linkClasses(route().current('products.*'))
                                "
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                    />
                                </svg>
                                <span>Products</span>
                            </Link>
                            <Link
                                v-if="canAccessCategories"
                                :href="route('categories.index')"
                                :class="
                                    linkClasses(route().current('categories.*'))
                                "
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                    />
                                </svg>
                                <span>Categories</span>
                            </Link>
                            <Link
                                v-if="canAccessProducts"
                                :href="route('stock.index')"
                                :class="linkClasses(route().current('stock.*'))"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"
                                    />
                                </svg>
                                <span>Stock</span>
                            </Link>
                        </div>
                    </div>

                    <!-- Reports & Logs -->
                    <div class="mt-6">
                        <div class="px-3 mb-2">
                            <p
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider"
                            >
                                Reports
                            </p>
                        </div>
                        <div class="space-y-1">
                            <Link
                                v-if="hasPermission('view-reports')"
                                :href="route('reports.sales')"
                                :class="
                                    linkClasses(route().current('reports.*'))
                                "
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    />
                                </svg>
                                <span>Reports</span>
                            </Link>
                            <Link
                                v-if="hasPermission('view-reports')"
                                :href="route('activity-logs.index')"
                                :class="
                                    linkClasses(
                                        route().current('activity-logs.*'),
                                    )
                                "
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                                    />
                                </svg>
                                <span>Activity Logs</span>
                            </Link>
                        </div>
                    </div>

                    <!-- Administration -->
                    <div class="mt-6">
                        <div class="px-3 mb-2">
                            <p
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider"
                            >
                                Administration
                            </p>
                        </div>
                        <div class="space-y-1">
                            <Link
                                v-if="canAccessUsers"
                                :href="route('users.index')"
                                :class="linkClasses(route().current('users.*'))"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                    />
                                </svg>
                                <span>Users</span>
                            </Link>
                            <Link
                                v-if="canAccessRoles"
                                :href="route('roles.index')"
                                :class="linkClasses(route().current('roles.*'))"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                    />
                                </svg>
                                <span>Roles</span>
                            </Link>
                            <Link
                                v-if="canAccessPermissions"
                                :href="route('permissions.index')"
                                :class="
                                    linkClasses(
                                        route().current('permissions.*'),
                                    )
                                "
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"
                                    />
                                </svg>
                                <span>Permissions</span>
                            </Link>
                            <Link
                                v-if="canAccessSettings"
                                :href="route('settings.index')"
                                :class="
                                    linkClasses(route().current('settings.*'))
                                "
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                    />
                                </svg>
                                <span>Settings</span>
                            </Link>
                        </div>
                    </div>

                    <!-- User Account -->
                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <div class="space-y-1">
                            <Link
                                :href="route('profile.edit')"
                                class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                    />
                                </svg>
                                <span>Profile</span>
                            </Link>
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-left text-sm font-medium text-red-600 hover:bg-red-50"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                    />
                                </svg>
                                <span>Log Out</span>
                            </Link>
                        </div>
                    </div>
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
