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
        Schema::create('academic_sessions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('name', 100);

            $table->date('start_date');

            $table->date('end_date');

            $table->boolean('is_current')->default(false);

            $table->boolean('status')->default(true);

            $table->text('description')->nullable();

            $table->timestamps();

            $table->softDeletes();

            // Indexes
            $table->index('school_id');
            $table->index('status');
            $table->index('is_current');
            $table->index(['school_id', 'status']);

            // One session name per school
            $table->unique(['school_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_sessions');
    }
};