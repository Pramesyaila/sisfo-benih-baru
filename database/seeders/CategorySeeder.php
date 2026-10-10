<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $tree = [
            'Padi' => [
                'description' => 'Benih padi berbagai varietas unggul.',
                'children' => [
                    'Padi Sawah' => 'Varietas padi untuk lahan sawah.',
                    'Padi Gogo' => 'Varietas padi untuk lahan gogo.',
                ],
            ],
            'Hortikultura' => [
                'description' => 'Benih dan bibit tanaman hortikultura.',
                'children' => [
                    'Sayuran' => 'Benih berbagai varietas untuk tanaman hortikultura.',
                    'Buah-buahan' => 'Benih tanaman buah.',
                    'Tanaman Hias' => 'Benih tanaman hias.',
                ],
            ],
            'Ayam KUB' => [
                'description' => 'Bibit dan telur Kampung Unggul Balitbangtan.',
                'children' => [
                    'Bibit Ayam' => 'Bibit ayam KUB.',
                    'Telur Tetas' => 'Telur tetas ayam KUB.',
                ],
            ],
        ];

        foreach ($tree as $name => $definition) {
            $category = Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'parent_id' => null, 'description' => $definition['description']]
            );

            foreach ($definition['children'] as $childName => $childDescription) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($childName)],
                    ['name' => $childName, 'parent_id' => $category->id, 'description' => $childDescription]
                );
            }
        }
    }
}
