<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'name'           => fake()->name(),
            'email'          => fake()->unique()->safeEmail(),
            'password'       => static::$password ??= Hash::make('password'),
            'role'           => fake()->randomElement(['waiters', 'kitchen', 'barista', 'kasir']),
            'is_active'      => true,
            'remember_token' => Str::random(10),
        ];
    }

    /** State: set role tertentu. */
    public function role(string $role): static
    {
        return $this->state(fn () => ['role' => $role]);
    }

    /** State: akun nonaktif. */
    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
