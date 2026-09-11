<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        if (! $admin) {
            return;
        }

        Announcement::updateOrCreate(
            ['title' => 'Mandatory Pre-Monsoon Preparedness & Swiftwater Certification Drive 2026'],
            [
                'user_id' => $admin->id,
                'content' => 'All district emergency officers and disaster responders are required to renew their Certified Swiftwater Navigation and Cyclone Shelter Management certifications prior to the upcoming cyclone season. Review your Competency Digital Twin to verify required credentials.',
                'target_role' => 'all',
                'is_pinned' => true,
                'published_at' => now()->subDays(3),
            ]
        );

        Announcement::updateOrCreate(
            ['title' => 'Launch of the Real-Time Capacity Simulator for State Directors'],
            [
                'user_id' => $admin->id,
                'content' => 'State and district disaster leadership can now utilize the Capacity Simulator to project personnel readiness, training duration, and batch scaling for major emergency operations.',
                'target_role' => 'admin',
                'is_pinned' => false,
                'published_at' => now()->subDays(5),
            ]
        );
    }
}
