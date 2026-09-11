<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simulation_scenarios', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code')->unique();
            $table->string('disaster_type'); // Cyclone, Flood, Chemical Spill, Earthquake, Critical Outage
            $table->text('description');
            $table->text('initial_situation');
            $table->json('decision_tree_data'); // Nodes: situation, choices -> consequence, points, next_node
            $table->enum('difficulty', ['Beginner', 'Intermediate', 'Advanced'])->default('Intermediate');
            $table->foreignId('target_competency_id')->nullable()->constrained('competencies')->nullOnDelete();
            $table->unsignedInteger('time_limit_minutes')->default(15);
            $table->unsignedInteger('passing_score')->default(75);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('simulation_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('scenario_id')->constrained('simulation_scenarios')->cascadeOnDelete();
            $table->string('current_node')->default('start');
            $table->json('path_taken')->nullable();
            $table->unsignedInteger('score')->default(0);
            $table->boolean('passed')->default(false);
            $table->text('final_feedback')->nullable();
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simulation_attempts');
        Schema::dropIfExists('simulation_scenarios');
    }
};
