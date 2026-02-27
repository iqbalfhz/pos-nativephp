<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdmin->roles()->syncWithoutDetaching([$superAdminRole->id]);
        }

        // Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $admin->roles()->syncWithoutDetaching([$adminRole->id]);
        }

        // Cashier 1
        $cashier1 = User::firstOrCreate(
            ['email' => 'cashier1@example.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('cashier123'),
                'email_verified_at' => now(),
            ]
        );

        $cashierRole = Role::where('name', 'Cashier')->first();
        if ($cashierRole) {
            $cashier1->roles()->syncWithoutDetaching([$cashierRole->id]);
        }

        // Cashier 2
        $cashier2 = User::firstOrCreate(
            ['email' => 'cashier2@example.com'],
            [
                'name' => 'Siti Nurhaliza',
                'password' => Hash::make('cashier123'),
                'email_verified_at' => now(),
            ]
        );

        if ($cashierRole) {
            $cashier2->roles()->syncWithoutDetaching([$cashierRole->id]);
        }

        // Cashier 3
        $cashier3 = User::firstOrCreate(
            ['email' => 'cashier3@example.com'],
            [
                'name' => 'Ahmad Rizki',
                'password' => Hash::make('cashier123'),
                'email_verified_at' => now(),
            ]
        );

        if ($cashierRole) {
            $cashier3->roles()->syncWithoutDetaching([$cashierRole->id]);
        }
    }
}

