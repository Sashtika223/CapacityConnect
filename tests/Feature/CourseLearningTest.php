<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseLearningTest extends TestCase
{
    use RefreshDatabase;

    public function test_trainee_can_enroll_in_published_course_and_complete_lesson(): void
    {
        $trainee = User::factory()->create(['role' => 'trainee', 'status' => 'active']);
        $trainer = User::factory()->create(['role' => 'trainer', 'status' => 'active']);

        $course = Course::create([
            'trainer_id' => $trainer->id,
            'title' => 'Emergency Evacuation Logistics',
            'slug' => 'emergency-evacuation-logistics',
            'short_description' => 'Course description',
            'full_description' => 'Full detailed course syllabus description',
            'category' => 'Disaster Management',
            'difficulty' => 'Intermediate',
            'duration_hours' => 10,
            'status' => 'published',
        ]);

        $module = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Module 1: Flood Evacuation Protocol',
            'sort_order' => 1,
        ]);

        $lesson = Lesson::create([
            'module_id' => $module->id,
            'title' => 'Lesson 1: Command Structure',
            'slug' => 'lesson-1-command-structure',
            'content_type' => 'text',
            'text_content' => 'Incident command system steps...',
            'duration_minutes' => 15,
            'sort_order' => 1,
        ]);

        // Enroll
        $response = $this->actingAs($trainee)->post(route('trainee.courses.enroll', $course->id));
        $response->assertRedirect(route('trainee.learning.player', ['course' => $course->id]));
        $this->assertDatabaseHas('enrollments', ['user_id' => $trainee->id, 'course_id' => $course->id]);

        // Complete Lesson
        $compResponse = $this->actingAs($trainee)->post(route('trainee.learning.lesson.complete', ['course' => $course->id, 'lesson' => $lesson->id]));
        $compResponse->assertStatus(302);
        $this->assertDatabaseHas('lesson_progress', ['lesson_id' => $lesson->id, 'is_completed' => true]);
    }
}
