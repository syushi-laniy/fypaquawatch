<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parameter;

class ParameterSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'pH', 'unit' => 'pH', 'status' => 'Active'],
            ['name' => 'Turbidity', 'unit' => 'NTU', 'status' => 'Active'],
            ['name' => 'Water Level', 'unit' => 'cm', 'status' => 'Active'],
        ];

        foreach ($rows as $row) {
            Parameter::updateOrCreate(['name' => $row['name']], $row);
        }
    }
}
