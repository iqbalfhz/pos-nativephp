<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil kategori yang sudah ada
        $makanan = Category::where('slug', 'makanan')->first();
        $minuman = Category::where('slug', 'minuman')->first();
        $elektronik = Category::where('slug', 'elektronik')->first();
        $alatTulis = Category::where('slug', 'alat-tulis')->first();
        $kesehatan = Category::where('slug', 'kesehatan')->first();
        $rumahTangga = Category::where('slug', 'rumah-tangga')->first();

        $products = [
            // Makanan
            [
                'category_id' => $makanan?->id,
                'name' => 'Indomie Goreng',
                'sku' => 'MKN-001',
                'barcode' => '8992388101015',
                'price' => 3500,
                'cost' => 2800,
                'stock' => 150,
                'min_stock' => 20,
                'is_active' => true,
            ],
            [
                'category_id' => $makanan?->id,
                'name' => 'Chitato Rasa Sapi Panggang',
                'sku' => 'MKN-002',
                'barcode' => '8992980001012',
                'price' => 10000,
                'cost' => 8500,
                'stock' => 80,
                'min_stock' => 15,
                'is_active' => true,
            ],
            [
                'category_id' => $makanan?->id,
                'name' => 'Oreo Original',
                'sku' => 'MKN-003',
                'barcode' => '8998866601016',
                'price' => 12000,
                'cost' => 10000,
                'stock' => 60,
                'min_stock' => 10,
                'is_active' => true,
            ],
            [
                'category_id' => $makanan?->id,
                'name' => 'Wafer Tango',
                'sku' => 'MKN-004',
                'barcode' => '8992933001018',
                'price' => 5000,
                'cost' => 4000,
                'stock' => 100,
                'min_stock' => 20,
                'is_active' => true,
            ],
            
            // Minuman
            [
                'category_id' => $minuman?->id,
                'name' => 'Aqua 600ml',
                'sku' => 'MNM-001',
                'barcode' => '8991002001015',
                'price' => 4000,
                'cost' => 3200,
                'stock' => 200,
                'min_stock' => 30,
                'is_active' => true,
            ],
            [
                'category_id' => $minuman?->id,
                'name' => 'Coca Cola 390ml',
                'sku' => 'MNM-002',
                'barcode' => '8992753001014',
                'price' => 6500,
                'cost' => 5200,
                'stock' => 120,
                'min_stock' => 24,
                'is_active' => true,
            ],
            [
                'category_id' => $minuman?->id,
                'name' => 'Teh Pucuk Harum 350ml',
                'sku' => 'MNM-003',
                'barcode' => '8991002130012',
                'price' => 5000,
                'cost' => 4000,
                'stock' => 150,
                'min_stock' => 30,
                'is_active' => true,
            ],
            [
                'category_id' => $minuman?->id,
                'name' => 'Pocari Sweat 350ml',
                'sku' => 'MNM-004',
                'barcode' => '8992745301019',
                'price' => 7500,
                'cost' => 6000,
                'stock' => 90,
                'min_stock' => 20,
                'is_active' => true,
            ],
            
            // Elektronik
            [
                'category_id' => $elektronik?->id,
                'name' => 'Baterai AA Alkaline (2pcs)',
                'sku' => 'ELK-001',
                'barcode' => '8886331234567',
                'price' => 15000,
                'cost' => 12000,
                'stock' => 50,
                'min_stock' => 10,
                'is_active' => true,
            ],
            [
                'category_id' => $elektronik?->id,
                'name' => 'Kabel USB Type-C 1m',
                'sku' => 'ELK-002',
                'barcode' => '8886341234568',
                'price' => 35000,
                'cost' => 25000,
                'stock' => 30,
                'min_stock' => 5,
                'is_active' => true,
            ],
            [
                'category_id' => $elektronik?->id,
                'name' => 'Earphone Basic',
                'sku' => 'ELK-003',
                'barcode' => '8886351234569',
                'price' => 25000,
                'cost' => 18000,
                'stock' => 40,
                'min_stock' => 8,
                'is_active' => true,
            ],
            
            // Alat Tulis
            [
                'category_id' => $alatTulis?->id,
                'name' => 'Pulpen Pilot Hitam',
                'sku' => 'ATK-001',
                'barcode' => '4902505123450',
                'price' => 3000,
                'cost' => 2200,
                'stock' => 100,
                'min_stock' => 20,
                'is_active' => true,
            ],
            [
                'category_id' => $alatTulis?->id,
                'name' => 'Pensil 2B Steadtler',
                'sku' => 'ATK-002',
                'barcode' => '4902505123451',
                'price' => 4000,
                'cost' => 3000,
                'stock' => 80,
                'min_stock' => 15,
                'is_active' => true,
            ],
            [
                'category_id' => $alatTulis?->id,
                'name' => 'Buku Tulis Sidu 38 Lembar',
                'sku' => 'ATK-003',
                'barcode' => '8992812345012',
                'price' => 5000,
                'cost' => 3800,
                'stock' => 120,
                'min_stock' => 25,
                'is_active' => true,
            ],
            [
                'category_id' => $alatTulis?->id,
                'name' => 'Penghapus Staedtler',
                'sku' => 'ATK-004',
                'barcode' => '4902505123452',
                'price' => 3500,
                'cost' => 2500,
                'stock' => 70,
                'min_stock' => 15,
                'is_active' => true,
            ],
            
            // Kesehatan
            [
                'category_id' => $kesehatan?->id,
                'name' => 'Masker Medis 3 Ply (10pcs)',
                'sku' => 'KSH-001',
                'barcode' => '8992776123456',
                'price' => 15000,
                'cost' => 11000,
                'stock' => 100,
                'min_stock' => 20,
                'is_active' => true,
            ],
            [
                'category_id' => $kesehatan?->id,
                'name' => 'Hand Sanitizer 60ml',
                'sku' => 'KSH-002',
                'barcode' => '8992776123457',
                'price' => 12000,
                'cost' => 9000,
                'stock' => 85,
                'min_stock' => 15,
                'is_active' => true,
            ],
            [
                'category_id' => $kesehatan?->id,
                'name' => 'Tissue Magic 100 Lembar',
                'sku' => 'KSH-003',
                'barcode' => '8992776123458',
                'price' => 8000,
                'cost' => 6000,
                'stock' => 90,
                'min_stock' => 18,
                'is_active' => true,
            ],
            
            // Rumah Tangga
            [
                'category_id' => $rumahTangga?->id,
                'name' => 'Sabun Cuci Piring Sunlight 750ml',
                'sku' => 'RMH-001',
                'barcode' => '8998866701013',
                'price' => 18000,
                'cost' => 14500,
                'stock' => 60,
                'min_stock' => 12,
                'is_active' => true,
            ],
            [
                'category_id' => $rumahTangga?->id,
                'name' => 'Pewangi Pakaian Molto 900ml',
                'sku' => 'RMH-002',
                'barcode' => '8992745401016',
                'price' => 22000,
                'cost' => 17500,
                'stock' => 50,
                'min_stock' => 10,
                'is_active' => true,
            ],
            [
                'category_id' => $rumahTangga?->id,
                'name' => 'Sapu Lidi',
                'sku' => 'RMH-003',
                'barcode' => '8992812345678',
                'price' => 15000,
                'cost' => 11000,
                'stock' => 40,
                'min_stock' => 8,
                'is_active' => true,
            ],
            [
                'category_id' => $rumahTangga?->id,
                'name' => 'Kantong Plastik Kresek Hitam (25pcs)',
                'sku' => 'RMH-004',
                'barcode' => '8992812345679',
                'price' => 10000,
                'cost' => 7500,
                'stock' => 80,
                'min_stock' => 15,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }
    }
}
