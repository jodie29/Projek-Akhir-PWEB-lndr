<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class, // PASTIKAN INI ADA
            ServiceSeeder::class,   // Tambahkan juga ServiceSeeder jika ada
            // ... seeder lainnya
        ]);
    }
}