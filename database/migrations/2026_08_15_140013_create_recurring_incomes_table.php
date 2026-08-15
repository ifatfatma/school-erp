<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_incomes', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            $table->foreignId('income_category_id')
                ->constrained('income_categories')
                ->cascadeOnDelete();

            $table->string('name');

            $table->string('reference_no')
                ->nullable();

            $table->string('received_from');

            $table->decimal('amount', 12, 2)
                ->default(0);

            $table->string('payment_method', 100)
                ->default('Cash');

            $table->enum('frequency', [
                'Daily',
                'Weekly',
                'Monthly',
                'Quarterly',
                'Yearly',
            ])->default('Monthly');

            $table->date('start_date');

            $table->date('end_date')
                ->nullable();

            $table->date('next_date')
                ->nullable();

            $table->boolean('auto_create')
                ->default(true);

            $table->enum('status', [
                'Active',
                'Paused',
                'Completed',
                'Cancelled',
            ])->default('Active');

            $table->text('description')
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

            $table->index([
                'school_id',
                'status'
            ]);

            $table->index([
                'school_id',
                'next_date'
            ]);

            $table->index([
                'school_id',
                'frequency'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'recurring_incomes'
        );
    }
};