<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('certificate_number')->unique();
            $table->string('verification_code', 64)->unique()->index();
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['valid', 'expiring_soon', 'expired', 'revoked'])->default('valid')->index();
            $table->string('file_path')->nullable();
            $table->text('revocation_reason')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
