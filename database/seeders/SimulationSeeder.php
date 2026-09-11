<?php

namespace Database\Seeders;

use App\Models\Competency;
use App\Models\SimulationScenario;
use Illuminate\Database\Seeder;

class SimulationSeeder extends Seeder
{
    public function run(): void
    {
        $cycloneComp = Competency::where('code', 'COMP-NDR-01')->first();
        $floodComp = Competency::where('code', 'COMP-NDR-02')->first();

        // SCENARIO 1: Category 4 Super Cyclone "VARUNA" Command Decision Tree
        $decisionTreeCyclone = [
            'node_1' => [
                'title' => 'T-Minus 24 Hours: Storm Surge Alert',
                'situation' => 'IMD has upgraded Cyclone VARUNA to Category 4 (wind speed 190 km/h) making landfall near Dhamra Port in 24 hours. Estimated surge is 4.2 meters. Local fishing hamlets are hesitant to leave fishing boats unattended.',
                'choices' => [
                    [
                        'id' => 'choice_1a',
                        'text' => 'Deploy police and NDRF teams to enforce mandatory evacuation immediately while securing boats at the inland anchorage.',
                        'points' => 30,
                        'consequence' => 'Decisive leadership: 8,500 fishermen evacuated safely to reinforced cyclone shelters with boats secured at designated anchorage.',
                        'next_node' => 'node_2',
                    ],
                    [
                        'id' => 'choice_1b',
                        'text' => 'Issue voluntary advisory flyers and postpone forced evacuation until wind speeds touch 80 km/h.',
                        'points' => 5,
                        'consequence' => 'Critical delay! Roads become impassable due to falling trees, leaving 2,000 residents stranded as storm surge begins.',
                        'next_node' => 'node_2',
                    ],
                ],
            ],
            'node_2' => [
                'title' => 'T-Minus 8 Hours: Hospital Generator Flooding Threat',
                'situation' => 'The sub-divisional hospital housing 40 intensive care patients reports that basement backup diesel generators are at risk of saltwater flooding as tidal levels rise.',
                'choices' => [
                    [
                        'id' => 'choice_2a',
                        'text' => 'Mobilize high-capacity submersible dewatering pumps and pre-install portable rooftop generators.',
                        'points' => 40,
                        'consequence' => 'Excellent operational foresight: ICU power remains uninterrupted throughout the eye of the storm.',
                        'next_node' => 'node_3',
                    ],
                    [
                        'id' => 'choice_2b',
                        'text' => 'Attempt full hospital road transfer of ICU patients in ambulances during 120 km/h gusts.',
                        'points' => 10,
                        'consequence' => 'Dangerous decision: Ambulances stalled in storm debris, compromising patient oxygen supplies.',
                        'next_node' => 'node_3',
                    ],
                ],
            ],
            'node_3' => [
                'title' => 'T-Plus 6 Hours Post-Landfall: Road Clearance & Epidemic Prevention',
                'situation' => 'The eye has passed. The highway is blocked by 120 uprooted banyan trees and water distribution lines have suffered a saline water breach.',
                'choices' => [
                    [
                        'id' => 'choice_3a',
                        'text' => 'Deploy chainsaw clearing teams in tandem with mobile water chlorination tankers and satellite VSAT communications.',
                        'points' => 30,
                        'consequence' => 'Rapid restoration: Main arterial supply corridor opened within 4 hours; zero waterborne cholera outbreaks recorded.',
                        'next_node' => 'end',
                    ],
                    [
                        'id' => 'choice_3b',
                        'text' => 'Wait for state contractors to arrive the following morning while rationing untreated borewell water.',
                        'points' => 0,
                        'consequence' => 'Severe failure: Relief bottleneck causes acute diarrhea outbreak among 400 shelter inmates.',
                        'next_node' => 'end',
                    ],
                ],
            ],
        ];

        SimulationScenario::updateOrCreate(
            ['code' => 'SIM-CYCLONE-01'],
            [
                'title' => 'Operation Sagar Suraksha: Category 4 Super Cyclone Response',
                'disaster_type' => 'Cyclone & Storm Surge',
                'description' => 'Dynamic real-time decision simulation testing high-stakes tactical leadership, population evacuation, critical infrastructure preservation, and post-landfall health stabilization.',
                'initial_situation' => 'You are the Incident Commander for Coastal Sector Alpha. Category 4 Cyclone VARUNA is tracking directly toward your jurisdiction.',
                'decision_tree_data' => $decisionTreeCyclone,
                'difficulty' => 'Intermediate',
                'target_competency_id' => $cycloneComp?->id,
                'time_limit_minutes' => 15,
                'passing_score' => 75,
                'is_active' => true,
            ]
        );

        // SCENARIO 2: Urban River Flash Flood & Embankment Breach
        $decisionTreeFlood = [
            'node_1' => [
                'title' => 'Midnight Embankment Seepage Alert',
                'situation' => 'River discharge has reached 900,000 cusecs. Sensor telemetry shows a 40-meter sand boiling seepage on the downstream left ring bund protecting Sector 9.',
                'choices' => [
                    [
                        'id' => 'choice_f1a',
                        'text' => 'Immediately deploy geo-textile bags and inverted sand filters while sounding siren Level 2 in low-lying wards.',
                        'points' => 35,
                        'consequence' => 'Bund successfully stabilized with 3,000 sandbags; 5,000 residents moved to elevated schools.',
                        'next_node' => 'node_2',
                    ],
                    [
                        'id' => 'choice_f1b',
                        'text' => 'Wait for morning daylight inspection before deploying heavy earthmovers.',
                        'points' => 5,
                        'consequence' => 'Catastrophic breach occurs at 03:00 AM, inundating Sector 9 with 6 feet of floodwaters.',
                        'next_node' => 'node_2',
                    ],
                ],
            ],
            'node_2' => [
                'title' => 'Stranded Colony Rescue in Night Currents',
                'situation' => '300 residents are stranded on second-floor balconies. 15-knot swiftwater is carrying submerged metallic sheet debris.',
                'choices' => [
                    [
                        'id' => 'choice_f2a',
                        'text' => 'Deploy motorized Inflatable Rescue Boats (IRBs) with spotter searchlights and tensioned guideline safety ropes.',
                        'points' => 45,
                        'consequence' => 'Flawless night rescue: All 300 citizens safely transferred to rescue barges without capsizing.',
                        'next_node' => 'end',
                    ],
                    [
                        'id' => 'choice_f2b',
                        'text' => 'Instruct residents to swim toward high-voltage transmission towers.',
                        'points' => 0,
                        'consequence' => 'Severe casualty hazard: Hypothermia and electrocution risk.',
                        'next_node' => 'end',
                    ],
                ],
            ],
        ];

        SimulationScenario::updateOrCreate(
            ['code' => 'SIM-FLOOD-01'],
            [
                'title' => 'Night Embankment Breach & Urban Swiftwater Rescue',
                'disaster_type' => 'Flash Flood & River Inundation',
                'description' => 'Test your engineering and tactical rescue decision making during high-flow river surge and urban flood response.',
                'initial_situation' => 'Monsoon cloudburst has pushed river levels past the danger mark. You are the on-duty Flood Response Commander.',
                'decision_tree_data' => $decisionTreeFlood,
                'difficulty' => 'Advanced',
                'target_competency_id' => $floodComp?->id,
                'time_limit_minutes' => 12,
                'passing_score' => 75,
                'is_active' => true,
            ]
        );
    }
}
