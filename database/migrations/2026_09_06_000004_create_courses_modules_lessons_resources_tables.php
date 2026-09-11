<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('short_description', 500);
            $table->longText('full_description');
            $table->string('category')->index(); // Emergency Response, Public Health, Logistics, Incident Command, Infrastructure
            $table->enum('difficulty', ['Beginner', 'Intermediate', 'Advanced', 'Expert'])->default('Beginner');
            $table->unsignedInteger('duration_hours')->default(5);
            $table->string('thumbnail')->nullable();
            $table->json('learning_objectives')->nullable();
            $table->json('prerequisites')->nullable();
            $table->text('completion_criteria')->nullable();
            $table->enum('status', ['draft', 'pending', 'published', 'rejected', 'archived'])->default('draft')->index();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('course_competencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('competency_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('target_level')->default(3);
            $table->timestamps();
            $table->unique(['course_id', 'competency_id']);
        });

        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(1);
            $table->timestamps();
        });

        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('course_modules')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->enum('content_type', ['text', 'video', 'document'])->default('text');
            $table->longText('text_content')->nullable();
            $table->string('video_url')->nullable();
            $table->unsignedInteger('duration_minutes')->default(15);
            $table->unsignedInteger('sort_order')->default(1);
            $table->boolean('is_free_preview')->default(false);
            $table->timestamps();
        });

        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->nullable()->constrained('course_modules')->nullOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('uploader_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_type', 50); // pdf, docx, pptx, zip, mp4, etc.
            $table->unsignedBigInteger('file_size'); // in bytes
            $table->boolean('is_private')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('course_modules');
        Schema::dropIfExists('course_competencies');
        Schema::dropIfExists('courses');
    }
};
