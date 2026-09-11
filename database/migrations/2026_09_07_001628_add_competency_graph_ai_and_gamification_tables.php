<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_competencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('competency_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('measured_level')->default(3);
            $table->timestamps();
            $table->unique(['assessment_id', 'competency_id']);
        });

        Schema::create('skill_competencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->foreignId('competency_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['skill_id', 'competency_id']);
        });

        Schema::create('competency_progress_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('competency_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('proficiency_level');
            $table->string('source')->default('observed');
            $table->text('evidence')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('recorded_at');
            $table->timestamps();
            $table->index(['user_id', 'competency_id', 'recorded_at']);
        });

        Schema::create('knowledge_graph_edges', function (Blueprint $table) {
            $table->id();
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');
            $table->string('relation');
            $table->string('target_type');
            $table->unsignedBigInteger('target_id');
            $table->string('label')->nullable();
            $table->timestamps();
            $table->index(['source_type', 'source_id']);
            $table->index(['target_type', 'target_id']);
            $table->index('relation');
        });

        Schema::create('gamification_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('points')->default(0);
            $table->unsignedInteger('level')->default(1);
            $table->timestamps();
        });

        Schema::create('gamification_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('event_type')->index();
            $table->unsignedInteger('points');
            $table->string('description');
            $table->nullableMorphs('source');
            $table->timestamps();
        });

        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('badge_id')->constrained()->cascadeOnDelete();
            $table->timestamp('earned_at');
            $table->timestamps();
            $table->unique(['user_id', 'badge_id']);
        });

        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_conversation_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['user', 'assistant', 'system']);
            $table->longText('content');
            $table->json('cited_resources')->nullable();
            $table->boolean('is_ai_generated')->default(false);
            $table->timestamps();
        });

        Schema::create('simulator_assumptions', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simulator_assumptions');
        Schema::dropIfExists('ai_messages');
        Schema::dropIfExists('ai_conversations');
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('gamification_events');
        Schema::dropIfExists('gamification_profiles');
        Schema::dropIfExists('knowledge_graph_edges');
        Schema::dropIfExists('competency_progress_logs');
        Schema::dropIfExists('skill_competencies');
        Schema::dropIfExists('assessment_competencies');
    }
};
