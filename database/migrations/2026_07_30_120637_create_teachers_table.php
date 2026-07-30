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
        Schema::create('teachers', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('employee_id', 50)->unique();

            $table->string('first_name');
            $table->string('last_name')->nullable();

            $table->string('gender', 20);

            $table->date('date_of_birth')->nullable();

            $table->string('email')->nullable()->unique();
            $table->string('phone', 20);

            $table->string('qualification')->nullable();
            $table->string('experience')->nullable();

            $table->date('joining_date');

            $table->text('address')->nullable();

            $table->string('photo')->nullable();

            $table->boolean('status')->default(true);

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

            $table->index([
                'school_id',
                'status'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};