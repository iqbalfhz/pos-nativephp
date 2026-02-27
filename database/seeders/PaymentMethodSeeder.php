<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            ['name' => 'Cash', 'type' => 'cash', 'is_active' => true],
            ['name' => 'Credit Card', 'type' => 'card', 'is_active' => true],
            ['name' => 'Debit Card', 'type' => 'debit', 'is_active' => true],
            ['name' => 'E-Wallet', 'type' => 'ewallet', 'is_active' => true],
            ['name' => 'Bank Transfer', 'type' => 'transfer', 'is_active' => true],
        ];

        foreach ($methods as $method) {
            PaymentMethod::firstOrCreate(
                ['name' => $method['name']],
                $method
            );
        }
    }
}
