<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_payments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            $table->foreignId('expense_id')
                ->constrained('expenses')
                ->cascadeOnDelete();

            $table->date('payment_date');

            $table->decimal(
                'amount',
                12,
                2
            )->default(0);

            $table->enum('payment_method', [
                'Cash',
                'Card',
                'Bank Transfer',
                'UPI',
                'Cheque',
                'Online',
                'Other',
            ])->default('Cash');

            $table->string('transaction_no')
                ->nullable();

            $table->string('bank_name')
                ->nullable();

            $table->string('cheque_no')
                ->nullable();

            $table->text('remarks')
                ->nullable();

            $table->enum('status', [
                'Success',
                'Pending',
                'Cancelled',
            ])->default('Success');

            $table->unsignedBigInteger('created_by')
                ->nullable();

            $table->unsignedBigInteger('updated_by')
                ->nullable();

            $table->timestamps();

            $table->index([
                'school_id',
                'expense_id',
            ]);

            $table->index([
                'school_id',
                'payment_date',
            ]);

            $table->index([
                'school_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'expense_payments'
        );
    }
};