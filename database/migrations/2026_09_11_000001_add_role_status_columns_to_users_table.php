<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('trainee')->after('email'); // admin | trainer | trainee
            $table->string('status')->default('active')->after('role'); // active | pending | approved | rejected | suspended
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete()->after('status');
            $table->string('phone')->nullable()->after('department_id');
            $table->timestamp('approved_at')->nullable()->after('phone');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('approved_at');
            $table->timestamp('rejected_at')->nullable()->after('approved_by');
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete()->after('rejected_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
            $table->dropConstrainedForeignId('approved_by');
            $table->dropConstrainedForeignId('rejected_by');
            $table->dropColumn(['role', 'status', 'phone', 'approved_at', 'rejected_at']);
        });
    }
};
