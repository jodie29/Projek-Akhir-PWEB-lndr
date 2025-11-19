<?php
namespace Database\Seeders;
// File: database/seeders/AdminUserSeeder.php
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash; // WAJIB ADA

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin PowerWash',
            'email' => 'admin@powerwash.test',
            'password' => Hash::make('password'), // PASTIKAN ADA Hash::make()
            'role' => 'admin', 
        ]);
    }
}