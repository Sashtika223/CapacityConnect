<?php

namespace Database\Seeders;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\CustomNotification;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoOperationalDataSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::where('status', 'published')->first();
        if (! $course) {
            return;
        }

        $trainees = User::where('role', 'trainee')->where('status', 'active')->take(3)->get();
        foreach ($trainees as $trainee) {
            Certificate::firstOrCreate(
                ['user_id' => $trainee->id, 'course_id' => $course->id],
                [
                    'certificate_number' => Certificate::generateNumber(),
                    'verification_code' => Certificate::generateVerificationCode(),
                    'issue_date' => now()->subDays(14)->toDateString(),
                    'status' => 'valid',
                ]
            );

            CustomNotification::firstOrCreate([
                'user_id' => $trainee->id,
                'title' => 'Welcome to Capacity Connect',
                'message' => "Your learning profile is ready. Explore {$course->title}.",
            ], [
                'type' => 'info',
                'action_url' => route('courses.show', $course->slug),
            ]);
        }
    }
}
