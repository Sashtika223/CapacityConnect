<?php

namespace Database\Seeders;

use App\Models\Competency;
use App\Models\Department;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Qualification;
use App\Models\Skill;
use App\Models\User;
use App\Models\UserCompetency;
use App\Models\UserSkill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::all();
        $ndrDept = $departments->firstWhere('code', 'NDR-TO');
        $mfDept = $departments->firstWhere('code', 'MF-EWS');
        $elDept = $departments->firstWhere('code', 'EL-SCM');

        // 1. Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@capacityconnect.gov'],
            [
                'name' => 'Dr. Rajeshwardhan Sharma (Director General)',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status' => 'active',
                'phone' => '+91-11-2345-6789',
                'department_id' => $ndrDept?->id,
                'email_verified_at' => now(),
            ]
        );

        Profile::updateOrCreate(['user_id' => $admin->id], [
            'job_title' => 'Chief Operations Director & Capacity Head',
            'bio' => '25+ years in National Disaster Response Command, Emergency Management Systems, and Institutional Capacity Building.',
            'location' => 'National Disaster Management Center, New Delhi',
            'emergency_unit' => 'Executive Crisis Cell',
        ]);

        // 2. Trainers
        $trainer1 = User::updateOrCreate(
            ['email' => 'trainer.cyclone@capacityconnect.gov'],
            [
                'name' => 'Dr. Ananya Mukherjee',
                'password' => Hash::make('password123'),
                'role' => 'trainer',
                'status' => 'active',
                'phone' => '+91-98112-33445',
                'department_id' => $mfDept?->id,
                'email_verified_at' => now(),
            ]
        );

        Profile::updateOrCreate(['user_id' => $trainer1->id], [
            'job_title' => 'Lead Meteorological Officer & Cyclone Specialist',
            'bio' => 'Senior Cyclone Specialist with 18 years experience training coastal disaster authorities across the Bay of Bengal.',
            'location' => 'Regional Meteorological Center, Kolkata',
            'emergency_unit' => 'Early Warning Division',
        ]);

        Qualification::create([
            'user_id' => $trainer1->id,
            'degree' => 'Ph.D. in Atmospheric Sciences & Tropical Meteorology',
            'institution' => 'Indian Institute of Science (IISc)',
            'field_of_study' => 'Atmospheric Fluid Dynamics',
            'graduation_year' => 2012,
        ]);

        Experience::create([
            'user_id' => $trainer1->id,
            'position' => 'Senior Warning Specialist',
            'organization' => 'National Cyclone Risk Mitigation Project',
            'start_date' => '2015-04-01',
            'is_current' => true,
            'description' => 'Directed evacuation warning models for multiple Super Cyclones and trained over 1,500 district officers.',
        ]);

        $trainer2 = User::updateOrCreate(
            ['email' => 'trainer.rescue@capacityconnect.gov'],
            [
                'name' => 'Col. Vikram Rathore (Retd.)',
                'password' => Hash::make('password123'),
                'role' => 'trainer',
                'status' => 'active',
                'phone' => '+91-98765-43210',
                'department_id' => $ndrDept?->id,
                'email_verified_at' => now(),
            ]
        );

        Profile::updateOrCreate(['user_id' => $trainer2->id], [
            'job_title' => 'Master Instructor - Tactical Rescue & Incident Command',
            'bio' => 'Former Battalion Commander, National Disaster Response Force. Expert in high-altitude and urban search operations.',
            'location' => 'Tactical Training Academy, Pune',
            'emergency_unit' => 'Rapid Deployment Wing',
        ]);

        $trainer3 = User::updateOrCreate(
            ['email' => 'trainer.pending@capacityconnect.gov'],
            [
                'name' => 'Priya Sundaram',
                'password' => Hash::make('password123'),
                'role' => 'trainer',
                'status' => 'pending', // Demo pending approval
                'phone' => '+91-94441-23890',
                'department_id' => $elDept?->id,
                'email_verified_at' => now(),
            ]
        );

        Profile::updateOrCreate(['user_id' => $trainer3->id], [
            'job_title' => 'Supply Chain Logistics Consultant',
            'bio' => 'Emergency shelter prepositioning and relief corridor architect.',
            'location' => 'Chennai Logistics Depot',
        ]);

        // 3. Trainees
        $trainees = [
            [
                'name' => 'Saurav Sen',
                'email' => 'trainee1@capacityconnect.gov',
                'title' => 'District Emergency Officer',
                'location' => 'Puri, Odisha',
                'dept_id' => $ndrDept?->id,
            ],
            [
                'name' => 'Meera Nair',
                'email' => 'trainee2@capacityconnect.gov',
                'title' => 'Hydro-Meteorologist Assistant',
                'location' => 'Kochi, Kerala',
                'dept_id' => $mfDept?->id,
            ],
            [
                'name' => 'Karan Patel',
                'email' => 'trainee3@capacityconnect.gov',
                'title' => 'Relief Operations Coordinator',
                'location' => 'Surat, Gujarat',
                'dept_id' => $elDept?->id,
            ],
            [
                'name' => 'Ayesha Khan',
                'email' => 'trainee4@capacityconnect.gov',
                'title' => 'Rapid Trauma Response Officer',
                'location' => 'Guwahati, Assam',
                'dept_id' => $ndrDept?->id,
            ],
        ];

        foreach ($trainees as $tData) {
            $trainee = User::updateOrCreate(
                ['email' => $tData['email']],
                [
                    'name' => $tData['name'],
                    'password' => Hash::make('password123'),
                    'role' => 'trainee',
                    'status' => 'active',
                    'department_id' => $tData['dept_id'],
                    'email_verified_at' => now(),
                ]
            );

            Profile::updateOrCreate(['user_id' => $trainee->id], [
                'job_title' => $tData['title'],
                'location' => $tData['location'],
                'bio' => 'Field professional focused on enhancing regional disaster readiness and emergency capacity.',
            ]);

            // Assign skills
            foreach (Skill::take(3)->get() as $sk) {
                UserSkill::updateOrCreate(
                    ['user_id' => $trainee->id, 'skill_id' => $sk->id],
                    ['proficiency_level' => rand(2, 4), 'verified' => true]
                );
            }

            // Assign competencies
            foreach (Competency::take(3)->get() as $comp) {
                UserCompetency::updateOrCreate(
                    ['user_id' => $trainee->id, 'competency_id' => $comp->id],
                    [
                        'proficiency_level' => rand(2, 4),
                        'evidence_notes' => 'Verified through field drill & certified course assessment.',
                        'verified_by' => $trainer1->id,
                        'assessed_at' => now()->subDays(rand(5, 45)),
                    ]
                );
            }
        }
    }
}
