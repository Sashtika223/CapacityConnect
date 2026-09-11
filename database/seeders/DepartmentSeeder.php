<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'National Disaster Response & Tactical Operations',
                'code' => 'NDR-TO',
                'description' => 'Coordination and deployment for rapid disaster rescue, flood evacuation, and frontline emergency response.',
                'icon' => 'shield',
            ],
            [
                'name' => 'Meteorological Forecasting & Early Warning Systems',
                'code' => 'MF-EWS',
                'description' => 'Advanced cyclone tracking, hydro-meteorological models, and multi-channel public warning dissemination.',
                'icon' => 'cloud-rain',
            ],
            [
                'name' => 'Emergency Logistics & Supply Chain Management',
                'code' => 'EL-SCM',
                'description' => 'Relief shelter provisioning, strategic warehousing, medical stockpiles, and remote access logistics.',
                'icon' => 'truck',
            ],
            [
                'name' => 'Critical Infrastructure & Urban Resilience',
                'code' => 'CI-UR',
                'description' => 'Bridge, power grid, water sanitation, and structural vulnerability assessment during natural calamities.',
                'icon' => 'building-office-2',
            ],
            [
                'name' => 'Public Health & Emergency Trauma Medicine',
                'code' => 'PH-ETM',
                'description' => 'Field triage, epidemic outbreak containment, mobile casualty care, and mental health response.',
                'icon' => 'heart',
            ],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(['code' => $dept['code']], $dept);
        }
    }
}
