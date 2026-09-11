<?php

namespace Database\Seeders;

use App\Models\Competency;
use App\Models\Department;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillAndCompetencySeeder extends Seeder
{
    public function run(): void
    {
        $ndr = Department::where('code', 'NDR-TO')->first();
        $mf = Department::where('code', 'MF-EWS')->first();
        $el = Department::where('code', 'EL-SCM')->first();
        $ci = Department::where('code', 'CI-UR')->first();
        $ph = Department::where('code', 'PH-ETM')->first();

        // 1. Skills
        $skills = [
            ['name' => 'Incident Command System (ICS)', 'code' => 'SK-ICS', 'category' => 'Command & Control', 'description' => 'Standardized on-scene emergency management hierarchy and operational protocol.'],
            ['name' => 'Hydrodynamic Flood Inundation Modeling', 'code' => 'SK-HFIM', 'category' => 'Meteorology', 'description' => 'GIS contour modeling and river breach projection analysis.'],
            ['name' => 'Tropical Cyclone Track Forecasting', 'code' => 'SK-TCTF', 'category' => 'Meteorology', 'description' => 'Interpreting satellite Doppler radar and atmospheric pressure gradient patterns.'],
            ['name' => 'Cold-Chain Medical Logistics', 'code' => 'SK-CCML', 'category' => 'Logistics', 'description' => 'Preserving vaccine and blood plasma potency across transit bottlenecks.'],
            ['name' => 'Mass Casualty Triage (START Protocol)', 'code' => 'SK-START', 'category' => 'Emergency Medicine', 'description' => 'Simple Triage and Rapid Treatment prioritization under field conditions.'],
            ['name' => 'Urban Search & Rescue (USAR) Acoustic Sonar', 'code' => 'SK-USAR', 'category' => 'Tactical Operations', 'description' => 'Locating survivors beneath collapsed reinforced concrete structures.'],
            ['name' => 'Emergency Satellite Telecommunications', 'code' => 'SK-SATCOM', 'category' => 'Technology', 'description' => 'Restoring VSAT data links and VHF/UHF tactical radio repeaters when terrestrial networks fail.'],
            ['name' => 'Hazardous Material (HAZMAT) Containment', 'code' => 'SK-HAZMAT', 'category' => 'Safety', 'description' => 'Chemical spill neutralisation and zone boundary decontamination.'],
        ];

        foreach ($skills as $s) {
            Skill::updateOrCreate(['code' => $s['code']], $s);
        }

        // 2. Competencies
        $competencies = [
            [
                'code' => 'COMP-NDR-01',
                'title' => 'Coastal Cyclone Evacuation Command',
                'category' => 'Disaster Response',
                'description' => 'Ability to execute large-scale population evacuations, shelter management, and storm surge barrier deployment.',
                'required_level' => 4,
                'department_id' => $ndr?->id,
            ],
            [
                'code' => 'COMP-MF-01',
                'title' => 'Early Warning & Risk Communication',
                'category' => 'Early Warning',
                'description' => 'Precision meteorological alerting, storm trajectory interpretation, and public advisory synchronization.',
                'required_level' => 4,
                'department_id' => $mf?->id,
            ],
            [
                'code' => 'COMP-EL-01',
                'title' => 'Emergency Relief Supply Chain Resilience',
                'category' => 'Logistics',
                'description' => 'Strategic warehousing, ration packet distribution routing, and air-drop logistics planning.',
                'required_level' => 3,
                'department_id' => $el?->id,
            ],
            [
                'code' => 'COMP-CI-01',
                'title' => 'Post-Earthquake Structural Assessment',
                'category' => 'Infrastructure',
                'description' => 'Rapid structural integrity tagging (Green, Yellow, Red) for lifeline buildings and hospital facilities.',
                'required_level' => 4,
                'department_id' => $ci?->id,
            ],
            [
                'code' => 'COMP-PH-01',
                'title' => 'Waterborne Epidemic Containment',
                'category' => 'Public Health',
                'description' => 'Surveillance, water testing, rapid chlorination, and mobile cholera treatment unit mobilization.',
                'required_level' => 3,
                'department_id' => $ph?->id,
            ],
            [
                'code' => 'COMP-NDR-02',
                'title' => 'Advanced Flood Rescue & Swiftwater Navigation',
                'category' => 'Disaster Response',
                'description' => 'Inflatable rescue boat operation in high-velocity flood currents and urban submerged zones.',
                'required_level' => 4,
                'department_id' => $ndr?->id,
            ],
        ];

        foreach ($competencies as $c) {
            Competency::updateOrCreate(['code' => $c['code']], $c);
        }
    }
}
