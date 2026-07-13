<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Satu akun per role untuk testing. Password semua: "password".
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Admin Pitou',   'email' => 'admin@pitoucafe.test',   'role' => 'admin'],
            ['name' => 'Waiter Pitou',  'email' => 'waiter@pitoucafe.test',  'role' => 'waiters'],
            ['name' => 'Kitchen Pitou', 'email' => 'kitchen@pitoucafe.test', 'role' => 'kitchen'],
            ['name' => 'Barista Pitou', 'email' => 'barista@pitoucafe.test', 'role' => 'barista'],
            ['name' => 'Kasir Pitou',   'email' => 'kasir@pitoucafe.test',   'role' => 'kasir'],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name'      => $u['name'],
                    'password'  => Hash::make('password'),
                    'role'      => $u['role'],
                    'is_active' => true,
                ]
            );
        }
    }
}
