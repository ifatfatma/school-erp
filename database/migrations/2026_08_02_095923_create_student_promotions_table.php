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
        Schema::create('student_promotions', function (Blueprint $table) {

            $table->id();

            // School
            $table->foreignId('school_id')
                ->constrained()
                ->cascadeOnDelete();

            // Student
            $table->foreignId('student_id')
                ->constrained()
                ->cascadeOnDelete();

            // Academic Sessions
            $table->foreignId('from_session_id')
                ->constrained('academic_sessions')
                ->cascadeOnDelete();

            $table->foreignId('to_session_id')
                ->constrained('academic_sessions')
                ->cascadeOnDelete();

            // Classes
            $table->foreignId('from_class_id')
                ->constrained('classes')
                ->cascadeOnDelete();

            $table->foreignId('to_class_id')
                ->constrained('classes')
                ->cascadeOnDelete();

            // Sections
            $table->foreignId('from_section_id')
                ->nullable()
                ->constrained('sections')
                ->nullOnDelete();

            $table->foreignId('to_section_id')
                ->nullable()
                ->constrained('sections')
                ->nullOnDelete();

            // Promotion Details
            $table->date('promotion_date');

            $table->enum('promotion_type', [
                'Promoted',
                'Repeated',
                'Transferred',
                'Graduated',
                'Dropped',
            ])->default('Promoted');

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

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index(['school_id', 'student_id']);

            $table->index(['from_session_id', 'to_session_id']);

            $table->index(['from_class_id', 'to_class_id']);

            $table->index('promotion_type');

            /*
            |--------------------------------------------------------------------------
            | Prevent Duplicate Promotion
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'student_id',
                'to_session_id'
            ], 'student_session_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_promotions');
    }
};