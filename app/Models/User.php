<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $role
 * WAJIB: DocBlock ini mengatasi error Intelephense "Undefined method 'orders'"
 * saat memanggil $customer->orders() di CustomerDashboardController
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'role', // PENTING: Pastikan 'role' ada di sini untuk memungkinkan pengisian massal
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        // NOTE: We intentionally do NOT cast 'password' => 'hashed' here because
        // existing code (seeders, controllers) already uses Hash::make() when
        // creating or updating users. Having both would double-hash passwords and
        // break authentication.
        // If you prefer to use automatic hashing at the model level, remove all
        // explicit Hash::make() calls and re-seed / update existing passwords.
    ];

    /**
     * Relasi: Satu User memiliki banyak Order.
     * Digunakan oleh Customer, jadi kita gunakan kunci asing 'customer_id'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders(): HasMany
    {
        // KOREKSI UTAMA: Menggunakan 'customer_id' untuk sinkronisasi dengan database/migration.
        return $this->hasMany(Order::class, 'customer_id');
    }

    /**
     * Relasi khusus untuk kurir: pesanan yang ditugaskan kepada kurir via 'courier_id'.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assignedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'courier_id');
    }
    
    /**
     * Helper untuk memeriksa peran pengguna (penting untuk RBAC).
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
}