<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::whereIn('name', [
            'Padi Sawah', 'Padi Gogo', 'Sayuran', 'Buah-buahan', 'Tanaman Hias', 'Bibit Ayam', 'Telur Tetas',
        ])->pluck('id', 'name');

        $products = [
            ['Padi Sawah', 'Padi Inpari 32', '5 kg', 'bungkus', 50000, 100],
            ['Padi Sawah', 'Padi Inpari 42', '5 kg', 'bungkus', 52000, 80],
            ['Padi Sawah', 'Padi Ciherang', '5 kg', 'bungkus', 48000, 60],
            ['Padi Gogo', 'Padi Inpago 8', '5 kg', 'bungkus', 51000, 70],
            ['Padi Gogo', 'Padi Inpago 12', '5 kg', 'bungkus', 53000, 65],
            ['Sayuran', 'Benih Cabai Rawit', '10 gram', 'sachet', 15000, 200],
            ['Sayuran', 'Benih Tomat Unggul', '10 gram', 'sachet', 17000, 150],
            ['Sayuran', 'Benih Kangkung', '10 gram', 'sachet', 12000, 120],
            ['Buah-buahan', 'Benih Melon', '10 gram', 'sachet', 18000, 90],
            ['Buah-buahan', 'Benih Semangka', '10 gram', 'sachet', 19000, 85],
            ['Buah-buahan', 'Benih Pepaya', '10 gram', 'sachet', 16000, 75],
            ['Tanaman Hias', 'Benih Bunga Melati', '5 gram', 'sachet', 14000, 60],
            ['Bibit Ayam', 'DOC Ayam KUB', '1 ekor', 'ekor', 8000, 300],
            ['Telur Tetas', 'Telur Ayam KUB', '10 butir', 'butir', 25000, 120],
        ];

        foreach ($products as [$categoryName, $name, $size, $unit, $price, $stock]) {
            $categoryId = $categories[$categoryName] ?? null;

            if (! $categoryId) {
                continue;
            }

            Product::updateOrCreate(
                ['name' => $name],
                [
                    'category_id' => $categoryId,
                    'slug' => Str::slug($name),
                    'description' => "Produk {$name} yang tersedia melalui katalog resmi instansi.",
                    'packaging_unit' => $unit,
                    'packaging_size' => $size,
                    'price' => $price,
                    'stock' => $stock,
                    'min_stock' => 15,
                    'status' => 'aktif',
                ]
            );
        }
    }
}
