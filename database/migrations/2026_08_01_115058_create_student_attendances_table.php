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
        Schema::create('student_attendances', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();

            $table->foreignId('section_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('attendance_date');

            $table->enum('status', [
                'Present',
                'Absent',
                'Late',
                'Half Day',
                'Leave',
            ])->default('Present');

            $table->time('check_in')->nullable();

            $table->time('check_out')->nullable();

            $table->text('remarks')->nullable();

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

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index(['school_id', 'attendance_date']);

            $table->index(['student_id', 'attendance_date']);

            $table->index(['class_id', 'section_id']);

            $table->index(['attendance_date', 'status']);

            /*
            |--------------------------------------------------------------------------
            | Prevent Duplicate Attendance
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'student_id',
                'attendance_date',
            ], 'student_attendance_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_attendances');
    }
};