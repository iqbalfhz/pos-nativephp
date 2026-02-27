<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            ['key' => 'store_name', 'value' => 'Toko Default', 'type' => 'string', 'description' => 'Nama toko'],
            ['key' => 'store_address', 'value' => 'Jl. Merdeka No. 1', 'type' => 'string', 'description' => 'Alamat toko'],
            ['key' => 'store_phone', 'value' => '085123456789', 'type' => 'string', 'description' => 'Nomor telepon toko'],
            ['key' => 'store_tax_id', 'value' => '00.000.000.0.000.000', 'type' => 'string', 'description' => 'NPWP/Tax ID'],
            ['key' => 'pb1_rate', 'value' => '10', 'type' => 'number', 'description' => 'Tarif pajak PB1 (%)'],
            ['key' => 'receipt_header', 'value' => 'Terima Kasih Telah Berbelanja', 'type' => 'string', 'description' => 'Header pada kuitansi'],
            ['key' => 'receipt_footer', 'value' => 'Kualitas adalah prioritas kami', 'type' => 'string', 'description' => 'Footer pada kuitansi'],
            ['key' => 'low_stock_threshold', 'value' => '10', 'type' => 'number', 'description' => 'Ambang minimum stok untuk notifikasi'],
            ['key' => 'enable_notifications', 'value' => 'true', 'type' => 'boolean', 'description' => 'Aktifkan notifikasi sistem'],
        ];

        foreach ($defaults as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
