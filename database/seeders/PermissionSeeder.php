<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $guard = config('auth.defaults.guard');

        // Define permissions with descriptions - CRUD pattern
        $permissionsData = [
            // Dashboard
            ['name' => 'view-dashboard', 'description' => 'View dashboard'],
            
            // Products
            ['name' => 'view-products', 'description' => 'View products list'],
            ['name' => 'create-products', 'description' => 'Create new products'],
            ['name' => 'edit-products', 'description' => 'Edit existing products'],
            ['name' => 'delete-products', 'description' => 'Delete products'],
            
            // Stock Management
            ['name' => 'view-stock', 'description' => 'View stock levels and history'],
            ['name' => 'adjust-stock', 'description' => 'Adjust stock quantities'],
            
            // Categories
            ['name' => 'view-categories', 'description' => 'View categories list'],
            ['name' => 'create-categories', 'description' => 'Create new categories'],
            ['name' => 'edit-categories', 'description' => 'Edit existing categories'],
            ['name' => 'delete-categories', 'description' => 'Delete categories'],
            
            // Discounts
            ['name' => 'view-discounts', 'description' => 'View discounts list'],
            ['name' => 'create-discounts', 'description' => 'Create new discounts'],
            ['name' => 'edit-discounts', 'description' => 'Edit existing discounts'],
            ['name' => 'delete-discounts', 'description' => 'Delete discounts'],
            
            // Payment Methods
            ['name' => 'view-payments', 'description' => 'View payment methods'],
            ['name' => 'create-payments', 'description' => 'Create payment methods'],
            ['name' => 'edit-payments', 'description' => 'Edit payment methods'],
            ['name' => 'delete-payments', 'description' => 'Delete payment methods'],
            
            // Sales/Cashier
            ['name' => 'process-sales', 'description' => 'Process sales transactions'],
            
            // Reports
            ['name' => 'view-reports', 'description' => 'View sales reports'],
            ['name' => 'export-reports', 'description' => 'Export reports to Excel/PDF'],
            
            // Activity Logs
            ['name' => 'view-activity-logs', 'description' => 'View activity logs'],
            
            // Users
            ['name' => 'view-users', 'description' => 'View users list'],
            ['name' => 'create-users', 'description' => 'Create new users'],
            ['name' => 'edit-users', 'description' => 'Edit existing users'],
            ['name' => 'delete-users', 'description' => 'Delete users'],
            
            // Roles
            ['name' => 'view-roles', 'description' => 'View roles list'],
            ['name' => 'create-roles', 'description' => 'Create new roles'],
            ['name' => 'edit-roles', 'description' => 'Edit existing roles'],
            ['name' => 'delete-roles', 'description' => 'Delete roles'],
            
            // Permissions
            ['name' => 'view-permissions', 'description' => 'View permissions list'],
            ['name' => 'create-permissions', 'description' => 'Create new permissions'],
            ['name' => 'edit-permissions', 'description' => 'Edit existing permissions'],
            ['name' => 'delete-permissions', 'description' => 'Delete permissions'],
            
            // Settings
            ['name' => 'view-settings', 'description' => 'View system settings'],
            ['name' => 'edit-settings', 'description' => 'Edit system settings'],
        ];

        // Create permissions
        $permissions = [];
        foreach ($permissionsData as $data) {
            $permissions[$data['name']] = Permission::firstOrCreate(
                ['name' => $data['name'], 'guard_name' => $guard],
                ['description' => $data['description']]
            );
        }

        // Create roles with descriptions
        $rolesData = [
            ['name' => 'Super Admin', 'description' => 'Has all permissions'],
            ['name' => 'Admin', 'description' => 'Administrator with most permissions'],
            ['name' => 'Cashier', 'description' => 'Can process sales only'],
        ];

        $rolesMap = [];
        foreach ($rolesData as $data) {
            $rolesMap[$data['name']] = Role::firstOrCreate(
                ['name' => $data['name'], 'guard_name' => $guard],
                ['description' => $data['description']]
            );
        }

        // Assign permissions to roles using Spatie methods
        if (isset($rolesMap['Super Admin'])) {
            $rolesMap['Super Admin']->syncPermissions(array_keys($permissions));
        }

        if (isset($rolesMap['Admin'])) {
            $rolesMap['Admin']->syncPermissions([
                'view-dashboard',
                'view-products',
                'create-products',
                'edit-products',
                'delete-products',
                'view-stock',
                'adjust-stock',
                'view-categories',
                'create-categories',
                'edit-categories',
                'delete-categories',
                'view-discounts',
                'create-discounts',
                'edit-discounts',
                'delete-discounts',
                'view-payments',
                'create-payments',
                'edit-payments',
                'delete-payments',
                'view-reports',
                'export-reports',
                'view-activity-logs',
                'view-settings',
                'edit-settings',
            ]);
        }

        if (isset($rolesMap['Cashier'])) {
            $rolesMap['Cashier']->syncPermissions([
                'view-dashboard',
                'process-sales',
                'view-products',
            ]);
        }
    }
}
