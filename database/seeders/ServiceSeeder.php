<?php
namespace Database\Seeders;
// File: database/seeders/ServiceSeeder.php
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        Service::create(['name' => 'Cuci Kering', 'price_per_kg' => 6000, 'active' => true]);
        Service::create(['name' => 'Cuci + Setrika', 'price_per_kg' => 8000, 'active' => true]);
        Service::create(['name' => 'Setrika Saja', 'price_per_kg' => 5000, 'active' => true]);
        Service::create(['name' => 'Kilat 1 Hari', 'price_per_kg' => 10000, 'active' => true]);
    }
}