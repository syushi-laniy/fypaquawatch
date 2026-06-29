<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Threshold;

class ThresholdSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['parameter' => 'pH', 'min_value' => '6.5', 'max_value' => '7.5'],
            ['parameter' => 'Turbidity', 'min_value' => '0', 'max_value' => '5'],
            ['parameter' => 'Water Level', 'min_value' => '20', 'max_value' => '30'],
        ];

        foreach ($rows as $row) {
            Threshold::updateOrCreate(['parameter' => $row['parameter']], $row);
        }
    }
}
