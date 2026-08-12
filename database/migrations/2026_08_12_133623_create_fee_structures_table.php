<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_structures', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            $table->foreignId('academic_session_id')
                ->constrained('academic_sessions')
                ->cascadeOnDelete();

            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();

            $table->foreignId('fee_type_id')
                ->constrained('fee_types')
                ->cascadeOnDelete();

            $table->decimal('amount', 10, 2)->default(0);

            /*
             * Used for monthly/quarterly etc.
             *
             * Example:
             * Tuition Fee = 1500
             * Admission Fee = 5000
             */

            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();

            $table->integer('due_day')
                ->nullable()
                ->comment('Day of month when fee becomes due');

            $table->decimal('late_fee', 10, 2)->default(0);

            $table->boolean('status')->default(true);

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
                'class_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_structures');
    }
};