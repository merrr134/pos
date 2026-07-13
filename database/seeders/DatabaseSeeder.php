<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,   // 5 akun per role
            MasterSeeder::class, // sample kategori, meja, menu (data awal)
        ]);
    }
}
