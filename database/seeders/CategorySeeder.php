<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $grades = [
            'Grade A' => 'Kelas A (kualitas terbaik).',
            'Grade B' => 'Kelas B (kualitas menengah).',
            'Grade C' => 'Kelas C (kualitas standar).',
        ];

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
                    'Bibit Ayam' => [
                        'description' => 'Bibit ayam KUB.',
                        'children' => $grades,
                    ],
                    'Telur Tetas' => [
                        'description' => 'Telur tetas ayam KUB.',
                        'children' => $grades,
                    ],
                ],
            ],
        ];

        $this->seedLevel($tree, null);
    }

    /**
     * Seed satu level pohon secara rekursif.
     *
     * Slug:
     *  - level 1 & 2 : slug nama (sama seperti sebelumnya, tidak menduplikasi data lama)
     *  - level 3     : slug nama induk + nama kelas, agar "Grade A" di tiap
     *                  varietas tidak saling menimpa (mis. bibit-ayam-grade-a).
     *
     * @param  array<string, array|string>  $nodes
     */
    private function seedLevel(array $nodes, ?Category $parent, string $slugPrefix = ''): void
    {
        foreach ($nodes as $name => $definition) {
            if (! is_array($definition)) {
                $definition = ['description' => $definition, 'children' => []];
            }

            $category = Category::updateOrCreate(
                ['slug' => Str::slug(trim($slugPrefix . ' ' . $name))],
                [
                    'name' => $name,
                    'parent_id' => $parent?->id,
                    'description' => $definition['description'] ?? null,
                ]
            );

            if (! empty($definition['children'])) {
                // Anak dari root memakai slug polos; anak dari varietas diberi prefix nama varietas.
                $childPrefix = $parent === null ? '' : $name;

                $this->seedLevel($definition['children'], $category, $childPrefix);
            }
        }
    }
}