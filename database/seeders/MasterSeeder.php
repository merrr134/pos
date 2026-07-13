<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Table;
use Illuminate\Database\Seeder;

/**
 * Data master awal (ringan) supaya halaman tidak kosong saat modul lanjutan dikerjakan.
 * Bukan fitur bisnis — hanya seed data.
 */
class MasterSeeder extends Seeder
{
    public function run(): void
    {
        // Kategori → menentukan station (BR-003)
        $makanan = Category::firstOrCreate(['name' => 'Makanan'], ['station' => 'kitchen']);
        $minuman = Category::firstOrCreate(['name' => 'Minuman'], ['station' => 'barista']);

        // Meja
        for ($i = 1; $i <= 6; $i++) {
            Table::firstOrCreate(['name' => "Meja $i"], ['status' => 'kosong']);
        }

        // Beberapa menu contoh
        $menus = [
            ['cat' => $makanan, 'name' => 'Nasi Goreng Spesial', 'price' => 25000],
            ['cat' => $makanan, 'name' => 'Nasi Kuning Spesial', 'price' => 22000],
            ['cat' => $minuman, 'name' => 'Kopi Susu Gula Aren', 'price' => 18000],
            ['cat' => $minuman, 'name' => 'Es Teh Manis Jumbo',  'price' => 8000],
        ];

        foreach ($menus as $m) {
            Menu::firstOrCreate(
                ['name' => $m['name']],
                [
                    'category_id'  => $m['cat']->id,
                    'price'        => $m['price'],
                    'is_available' => true,
                ]
            );
        }
    }
}
