<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->nullable()->constrained('course_modules')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['mcq', 'practical', 'final'])->default('mcq');
            $table->unsignedInteger('time_limit_minutes')->default(20);
            $table->unsignedTinyInteger('passing_percentage')->default(70);
            $table->unsignedTinyInteger('max_attempts')->default(3);
            $table->decimal('negative_marks_per_question', 4, 2)->default(0.00);
            $table->boolean('is_randomized')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('competency_id')->nullable()->constrained()->nullOnDelete();
            $table->text('question_text');
            $table->text('explanation')->nullable();
            $table->decimal('marks', 4, 2)->default(1.00);
            $table->enum('difficulty', ['Easy', 'Medium', 'Hard'])->default('Medium');
            $table->unsignedInteger('sort_order')->default(1);
            $table->timestamps();
        });

        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('sort_order')->default(1);
            $table->timestamps();
        });

        Schema::create('assessment_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('attempt_number')->default(1);
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('submitted_at')->nullable();
            $table->decimal('score', 6, 2)->default(0.00);
            $table->decimal('total_possible_marks', 6, 2)->default(0.00);
            $table->decimal('percentage', 5, 2)->default(0.00);
            $table->boolean('passed')->default(false);
            $table->timestamps();
        });

        Schema::create('assessment_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('selected_option_id')->nullable()->constrained('question_options')->nullOnDelete();
            $table->boolean('is_correct')->default(false);
            $table->decimal('marks_awarded', 4, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('practical_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('competency_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->longText('scenario_prompt');
            $table->longText('evaluation_criteria');
            $table->unsignedInteger('max_score')->default(100);
            $table->unsignedInteger('passing_score')->default(70);
            $table->timestamps();
        });

        Schema::create('practical_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practical_assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->longText('submission_text');
            $table->string('file_path')->nullable();
            $table->enum('status', ['submitted', 'under_review', 'passed', 'needs_revision', 'failed'])->default('submitted')->index();
            $table->unsignedInteger('score')->nullable();
            $table->text('feedback')->nullable();
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practical_submissions');
        Schema::dropIfExists('practical_assessments');
        Schema::dropIfExists('assessment_answers');
        Schema::dropIfExists('assessment_attempts');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('assessments');
    }
};
