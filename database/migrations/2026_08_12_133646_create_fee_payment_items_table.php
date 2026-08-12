<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_payment_items', function (Blueprint $table) {

            $table->id();

            $table->foreignId('fee_payment_id')
                ->constrained('fee_payments')
                ->cascadeOnDelete();

            $table->foreignId('student_fee_due_id')
                ->constrained('student_fee_dues')
                ->cascadeOnDelete();

            $table->decimal('amount', 10, 2)->default(0);

            $table->timestamps();

            $table->index([
                'fee_payment_id',
                'student_fee_due_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_payment_items');
    }
};