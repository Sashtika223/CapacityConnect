<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->string('category')->index(); // e.g. Disaster Response, Technology, Logistics, Medical, Leadership
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('competencies', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->string('category')->index(); // e.g. Emergency Planning, Incident Command, GIS & Mapping, First Aid
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('required_level')->default(3); // 1-Beginner, 2-Basic, 3-Intermediate, 4-Advanced, 5-Expert
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('user_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('proficiency_level')->default(1); // 1 to 5
            $table->boolean('verified')->default(false);
            $table->timestamps();
            $table->unique(['user_id', 'skill_id']);
        });

        Schema::create('user_competencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('competency_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('proficiency_level')->default(1); // 1 to 5
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('evidence_notes')->nullable();
            $table->timestamp('assessed_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'competency_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_competencies');
        Schema::dropIfExists('user_skills');
        Schema::dropIfExists('competencies');
        Schema::dropIfExists('skills');
    }
};
