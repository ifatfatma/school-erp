<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {

            $table->id();

            // School
            $table->foreignId('school_id')
                ->constrained()
                ->cascadeOnDelete();

            // Academic Session
            $table->foreignId('academic_session_id')
                ->constrained('academic_sessions')
                ->cascadeOnDelete();

            // Exam Type
            $table->foreignId('exam_type_id')
                ->constrained('exam_types')
                ->cascadeOnDelete();

            // Basic Information
            $table->string('name');
            $table->string('code',50)->nullable();

            // Dates
            $table->date('start_date');
            $table->date('end_date');

            // Result Dates
            $table->date('result_publish_date')->nullable();

            // Marks Entry
            $table->boolean('is_marks_locked')->default(false);

            // Status
            $table->boolean('status')->default(true);

            // Remarks
            $table->text('remarks')->nullable();

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

            // Prevent duplicate exam names within a session
            $table->unique([
                'school_id',
                'academic_session_id',
                'exam_type_id',
                'name'
            ], 'exam_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};