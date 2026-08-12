<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_payments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            $table->foreignId('academic_session_id')
                ->constrained('academic_sessions')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->string('receipt_no', 100);

            $table->date('payment_date');

            $table->decimal('total_amount', 10, 2)->default(0);

            $table->enum('payment_method', [
                'Cash',
                'Card',
                'Bank Transfer',
                'UPI',
                'Cheque',
                'Online',
                'Other'
            ])->default('Cash');

            $table->string('transaction_no', 150)->nullable();

            $table->string('bank_name')->nullable();

            $table->string('cheque_no', 100)->nullable();

            $table->text('remarks')->nullable();

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
                'student_id',
                'payment_date'
            ]);

            $table->unique([
                'school_id',
                'receipt_no'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_payments');
    }
};