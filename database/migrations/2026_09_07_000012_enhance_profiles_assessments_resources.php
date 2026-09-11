<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // -----------------------------------------------------------
        // 1. Profiles: add employee_id and avatar_path columns
        // -----------------------------------------------------------
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('employee_id', 20)->nullable()->unique()->after('user_id');
            $table->string('avatar_path')->nullable()->after('avatar');
            $table->string('designation')->nullable()->after('job_title');
            $table->string('areas_of_expertise')->nullable()->after('designation');
        });

        // -----------------------------------------------------------
        // 2. Questions: add topic column for categorisation
        // -----------------------------------------------------------
        Schema::table('questions', function (Blueprint $table) {
            $table->string('topic', 100)->nullable()->after('difficulty');
        });

        // -----------------------------------------------------------
        // 3. Resources: add download_count for usage tracking
        // -----------------------------------------------------------
        Schema::table('resources', function (Blueprint $table) {
            $table->unsignedBigInteger('download_count')->default(0)->after('is_private');
        });

        // -----------------------------------------------------------
        // 4. Assessment attempts: add started_at_epoch for server-
        //    side timer enforcement
        // -----------------------------------------------------------
        Schema::table('assessment_attempts', function (Blueprint $table) {
            $table->unsignedInteger('time_limit_minutes')->nullable()->after('attempt_number');
            $table->boolean('time_expired')->default(false)->after('passed');
        });

        // -----------------------------------------------------------
        // 5. Enrollments: add unenrolled_at for soft unenrol tracking
        // -----------------------------------------------------------
        Schema::table('enrollments', function (Blueprint $table) {
            $table->timestamp('unenrolled_at')->nullable()->after('last_accessed_at');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn('unenrolled_at');
        });

        Schema::table('assessment_attempts', function (Blueprint $table) {
            $table->dropColumn(['time_limit_minutes', 'time_expired']);
        });

        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn('download_count');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('topic');
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['employee_id', 'avatar_path', 'designation', 'areas_of_expertise']);
        });
    }
};
