<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AutomationRule;

class AutomationRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'name' => 'pH Low',
                'trigger' => 'pH < 6.5',
                'action' => 'pH Up Dosing (Peristaltic Pump)',
            ],
            [
                'name' => 'pH High',
                'trigger' => 'pH > 7.5',
                'action' => 'pH Down Dosing (Peristaltic Pump)',
            ],
            [
                'name' => 'High Turbidity',
                'trigger' => 'Turbidity > 5 NTU',
                'action' => 'Alert: Check filter condition',
            ],
            [
                'name' => 'Low Water Level',
                'trigger' => 'Water Level < 20 cm',
                'action' => 'Auto Top-up Pump (Submersible Pump)',
            ],
            [
                'name' => 'Scheduled Feeding',
                'trigger' => 'Time-based schedule',
                'action' => 'Auto Feeder Dispense',
            ],
        ];

        foreach ($rows as $row) {
            AutomationRule::updateOrCreate(
                ['name' => $row['name'], 'trigger' => $row['trigger']],
                $row
            );
        }
    }
}
