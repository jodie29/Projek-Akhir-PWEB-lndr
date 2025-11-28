<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Tambahkan import ini

class Service extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Berdasarkan ServiceSeeder Anda, field yang diisi adalah:
     * name, price_per_kg, dan active.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'price_per_kg',
        'active',
    ];
    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Relasi: Satu Layanan (Service) memiliki banyak Order.
     * Diasumsikan tabel 'orders' memiliki foreign key 'service_id'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}