<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('online_admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('application_no')->unique();
            $table->foreignId('academic_session_id')->nullable()->constrained('academic_sessions')->nullOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->date('dob')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->default('male');
            $table->string('blood_group')->nullable();
            $table->string('religion')->nullable();
            $table->string('category')->nullable();
            $table->string('aadhaar_no')->nullable();
            $table->string('mobile');
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('father_name')->nullable();
            $table->string('father_mobile')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_mobile')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_mobile')->nullable();
            $table->string('previous_school')->nullable();
            $table->string('previous_class')->nullable();
            $table->string('previous_marks')->nullable();
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected', 'enrolled'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_admissions');
    }
};
