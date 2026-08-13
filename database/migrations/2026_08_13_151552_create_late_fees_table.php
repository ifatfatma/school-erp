<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('late_fees', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            $table->string('name');

            $table->string('code')
                ->nullable();

            $table->enum('late_fee_type', [
                'Fixed',
                'Percentage',
            ]);

            $table->decimal(
                'value',
                10,
                2
            )->default(0);

            $table->unsignedInteger(
                'grace_days'
            )->default(0);

            $table->decimal(
                'max_amount',
                10,
                2
            )->nullable();

            $table->boolean('per_day')
                ->default(false);

            $table->text('description')
                ->nullable();

            $table->boolean('status')
                ->default(true);

            $table->unsignedBigInteger(
                'created_by'
            )->nullable();

            $table->unsignedBigInteger(
                'updated_by'
            )->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index([
                'school_id',
                'status',
            ]);

            $table->index([
                'school_id',
                'code',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('late_fees');
    }
};