<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('income_payments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            $table->foreignId('income_id')
                ->constrained('incomes')
                ->cascadeOnDelete();

            $table->string('receipt_no', 100);

            $table->date('payment_date');

            $table->decimal('amount', 12, 2)
                ->default(0);

            $table->string('payment_method', 100)
                ->default('Cash');

            $table->string('transaction_no', 150)
                ->nullable();

            $table->string('bank_name')
                ->nullable();

            $table->string('cheque_no', 100)
                ->nullable();

            $table->text('remarks')
                ->nullable();

            $table->enum('status', [
                'Success',
                'Pending',
                'Cancelled'
            ])->default('Success');

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
                'income_id'
            ]);

            $table->index([
                'school_id',
                'payment_date'
            ]);

            $table->index([
                'school_id',
                'status'
            ]);

            $table->unique([
                'school_id',
                'receipt_no'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'income_payments'
        );
    }
};