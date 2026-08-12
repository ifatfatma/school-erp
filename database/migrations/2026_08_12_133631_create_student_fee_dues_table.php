<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_fee_dues', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            $table->foreignId('academic_session_id')
                ->constrained('academic_sessions')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();

            $table->foreignId('section_id')
                ->nullable()
                ->constrained('sections')
                ->nullOnDelete();

            $table->foreignId('fee_type_id')
                ->constrained('fee_types')
                ->cascadeOnDelete();

            $table->foreignId('fee_structure_id')
                ->nullable()
                ->constrained('fee_structures')
                ->nullOnDelete();

            /*
             * Example:
             *
             * 2026-06-01
             * 2026-07-01
             *
             * Used for identifying monthly/quarterly dues.
             */
            $table->date('fee_date');

            $table->date('due_date')->nullable();

            $table->string('period', 50)->nullable()
                ->comment('Example: June 2026');

            $table->decimal('amount', 10, 2)->default(0);

            $table->decimal('discount', 10, 2)->default(0);

            $table->decimal('late_fee', 10, 2)->default(0);

            $table->decimal('net_amount', 10, 2)->default(0);

            $table->decimal('paid_amount', 10, 2)->default(0);

            $table->decimal('balance_amount', 10, 2)->default(0);

            $table->enum('status', [
                'Pending',
                'Partial',
                'Paid',
                'Cancelled'
            ])->default('Pending');

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

            $table->index([
                'school_id',
                'academic_session_id',
                'student_id'
            ]);

            $table->index([
                'student_id',
                'status'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_fee_dues');
    }
};