<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {

            $table->id();

            // School
            $table->foreignId('school_id')
                ->constrained()
                ->cascadeOnDelete();

            // Academic
            $table->foreignId('class_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('section_id')
                ->constrained()
                ->cascadeOnDelete();

            // Student Information
            $table->string('admission_no')->unique();
            $table->string('roll_no')->nullable();

            $table->string('first_name');
            $table->string('last_name')->nullable();

            $table->enum('gender', [
                'Male',
                'Female',
                'Other'
            ]);

            $table->date('dob')->nullable();

            $table->date('admission_date');

            $table->string('blood_group')->nullable();

            $table->string('religion')->nullable();

            $table->string('category')->nullable();

            $table->string('aadhaar_no')->nullable();

            // Contact
            $table->string('mobile',20)->nullable();

            $table->string('email')->nullable();

            $table->text('address')->nullable();

            // Parents
            $table->string('father_name');

            $table->string('father_mobile')->nullable();

            $table->string('father_occupation')->nullable();

            $table->string('mother_name')->nullable();

            $table->string('mother_mobile')->nullable();

            $table->string('guardian_name')->nullable();

            $table->string('guardian_mobile')->nullable();

            // Photo
            $table->string('photo')->nullable();

            // Status
            $table->boolean('status')->default(true);

            // Audit
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->softDeletes();

            $table->timestamps();

            $table->index([
                'school_id',
                'class_id',
                'section_id'
            ]);

            $table->index([
                'admission_no'
            ]);

            $table->index([
                'father_mobile'
            ]);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};