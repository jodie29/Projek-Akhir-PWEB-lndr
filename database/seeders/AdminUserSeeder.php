<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- 1. ADMIN USERS ---
        User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@powerwash.com',
            'password' => Hash::make('12345678'), // Password: 12345678
            'phone' => '081200000001',
            'address' => 'Kantor Pusat Admin 1',
            'role' => 'admin',
        ]);
        
        User::create([
            'name' => 'Admin Pembantu',
            'email' => 'admin2@powerwash.com',
            'password' => Hash::make('12345678'), // Password: 12345678
            'phone' => '081200000002',
            'address' => 'Kantor Cabang Admin 2',
            'role' => 'admin',
        ]);
        
        // --- 2. COURIER USERS ---
        User::create([
            'name' => 'Kurir Cepat Asep',
            'email' => 'courier@powerwash.com',
            'password' => Hash::make('12345678'), // Password: 12345678
            'phone' => '082300000001',
            'address' => 'Basis Kurir 1',
            'role' => 'courier',
        ]);

        User::create([
            'name' => 'Kurir Kilat Budi',
            'email' => 'courier2@powerwash.com',
            'password' => Hash::make('12345678'), // Password: 12345678
            'phone' => '082300000002',
            'address' => 'Basis Kurir 2',
            'role' => 'courier',
        ]);

        // --- 3. CUSTOMER USERS ---
        User::create([
            'name' => 'Pelanggan Setia Rina',
            'email' => 'customer@powerwash.com',
            'password' => Hash::make('12345678'), // Password: 12345678
            'phone' => '083400000001',
            'address' => 'Rumah Pelanggan 1',
            'role' => 'customer',
        ]);
        
        User::create([
            'name' => 'Pelanggan Baru Dedi',
            'email' => 'customer2@powerwash.com',
            'password' => Hash::make('12345678'), // Password: 12345678
            'phone' => '083400000002',
            'address' => 'Apartemen Pelanggan 2',
            'role' => 'customer',
        ]);
    }
}