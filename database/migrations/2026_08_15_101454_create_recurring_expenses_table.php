<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_expenses', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            $table->foreignId('expense_category_id')
                ->constrained('expense_categories')
                ->restrictOnDelete();

            $table->string('name');

            $table->string('payee_name');

            $table->decimal(
                'amount',
                12,
                2
            )->default(0);

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

            $table->date('next_due_date');

            $table->string('payment_method')
                ->default('Cash');

            $table->text('description')
                ->nullable();

            $table->boolean('auto_create')
                ->default(true);

            $table->enum('status', [
                'Active',
                'Paused',
                'Completed',
            ])->default('Active');

            $table->unsignedBigInteger('created_by')
                ->nullable();

            $table->unsignedBigInteger('updated_by')
                ->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index([
                'school_id',
                'next_due_date',
            ]);

            $table->index([
                'school_id',
                'status',
            ]);

            $table->index([
                'school_id',
                'expense_category_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'recurring_expenses'
        );
    }
};