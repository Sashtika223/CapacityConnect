<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_content_generations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('topic');
            $table->enum('type', ['course_description', 'learning_objectives', 'lesson_summary', 'mcqs', 'practice_questions'])->index();
            $table->string('difficulty')->default('Intermediate');
            $table->foreignId('competency_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->longText('prompt');
            $table->longText('generated_content');
            $table->enum('status', ['ai_generated', 'under_review', 'approved', 'rejected', 'published'])->default('ai_generated')->index();
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_content_generations');
    }
};
