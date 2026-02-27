<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'manage-users',
            'manage-products',
            'manage-categories',
            'manage-discounts',
            'manage-payments',
            'view-reports',
            'process-sales',
            'manage-settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::where('name', 'Super Admin')->first();
        $admin = Role::where('name', 'Admin')->first();
        $cashier = Role::where('name', 'Cashier')->first();

        if ($superAdmin) {
            $superAdmin->permissions()->sync(Permission::pluck('id'));
        }

        if ($admin) {
            $admin->permissions()->sync(Permission::whereIn('name', [
                'manage-products',
                'manage-categories',
                'manage-discounts',
                'manage-payments',
                'view-reports',
                'manage-settings',
            ])->pluck('id'));
        }

        if ($cashier) {
            $cashier->permissions()->sync(Permission::whereIn('name', [
                'process-sales',
            ])->pluck('id'));
        }
    }
}
