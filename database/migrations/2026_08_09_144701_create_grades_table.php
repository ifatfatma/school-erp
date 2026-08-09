<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            $table->string('name', 20);

            $table->decimal('min_percentage', 5, 2);

            $table->decimal('max_percentage', 5, 2);

            $table->decimal('grade_point', 5, 2)
                ->default(0);

            $table->boolean('is_pass')
                ->default(true);

            $table->text('description')
                ->nullable();

            $table->boolean('status')
                ->default(true);

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

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};