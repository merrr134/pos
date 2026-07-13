<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'  => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi
    |--------------------------------------------------------------------------
    */

    /** Order yang dibuat user ini (Waiters). */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'created_by');
    }

    /** Pembayaran yang diterima user ini (Kasir). */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'received_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Role
    |--------------------------------------------------------------------------
    */

    /** Nama route dashboard sesuai role (dipakai saat login & root route). */
    public function dashboardRoute(): string
    {
        return match ($this->role) {
            'admin'   => 'admin.dashboard',
            'waiters' => 'waiter.dashboard',
            'kitchen' => 'kitchen.index',
            'barista' => 'barista.index',
            'kasir'   => 'cashier.index',
            default   => 'login',
        };
    }

    public function isRole(string $role): bool
    {
        return $this->role === $role;
    }
}
