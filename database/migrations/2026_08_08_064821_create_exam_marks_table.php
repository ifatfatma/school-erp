<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_marks', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            $table->foreignId('exam_schedule_id')
                ->constrained('exam_schedules')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->decimal('theory_marks', 8, 2)
                ->default(0);

            $table->decimal('practical_marks', 8, 2)
                ->default(0);

            $table->decimal('viva_marks', 8, 2)
                ->default(0);

            $table->decimal('project_marks', 8, 2)
                ->default(0);

            $table->decimal('total_marks', 8, 2)
                ->default(0);

            $table->string('attendance', 20)
                ->default('Present');

            $table->text('remarks')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->softDeletes();

            $table->unique([
                'exam_schedule_id',
                'student_id'
            ]);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_marks');
    }
};