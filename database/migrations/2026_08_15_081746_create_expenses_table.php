<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            $table->foreignId('expense_category_id')
                ->constrained('expense_categories')
                ->restrictOnDelete();

            $table->date('expense_date');

            $table->string('expense_no')
                ->nullable();

            $table->string('payee_name');

            $table->string('payment_method')
                ->default('Cash');

            $table->decimal(
                'amount',
                12,
                2
            )->default(0);

            $table->string('reference_no')
                ->nullable();

            $table->text('description')
                ->nullable();

            $table->string('attachment')
                ->nullable();

            $table->enum('status', [
                'Paid',
                'Pending',
                'Cancelled',
            ])->default('Paid');

            $table->unsignedBigInteger('created_by')
                ->nullable();

            $table->unsignedBigInteger('updated_by')
                ->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index([
                'school_id',
                'expense_date',
            ]);

            $table->index([
                'school_id',
                'expense_category_id',
            ]);

            $table->index([
                'school_id',
                'status',
            ]);

            $table->index([
                'school_id',
                'payment_method',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};