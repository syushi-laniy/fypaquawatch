<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'Premium Fish Food (500g)', 'category' => 'Fish Food', 'price' => 18.90, 'stock' => 30],
            ['name' => 'Color Booster Pellets', 'category' => 'Fish Food', 'price' => 24.50, 'stock' => 20],
            ['name' => 'pH Up (Alkalinity Booster)', 'category' => 'Water Care', 'price' => 16.00, 'stock' => 15],
            ['name' => 'pH Down (Acidifier)', 'category' => 'Water Care', 'price' => 16.00, 'stock' => 15],
            ['name' => 'Aquarium Decor Set', 'category' => 'Decoration', 'price' => 35.00, 'stock' => 8],
            ['name' => 'Peristaltic Dosing Pump (pH)', 'category' => 'Equipment', 'price' => 45.00, 'stock' => 12],
            ['name' => 'Submersible Water Pump', 'category' => 'Equipment', 'price' => 49.90, 'stock' => 10],
            ['name' => 'Canister Filter Cartridge', 'category' => 'Equipment', 'price' => 25.00, 'stock' => 20],
        ];

        foreach ($rows as $row) {
            Product::updateOrCreate(['name' => $row['name']], $row);
        }
    }
}
