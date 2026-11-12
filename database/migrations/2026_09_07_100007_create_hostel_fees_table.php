<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('hostel_room_id')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('fee_type')->default('Monthly Fee'); // Monthly Fee, Admission Fee, Caution Deposit, Mess Fee, Other
            $table->string('month')->nullable(); // e.g. 2026-09
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('fine', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->string('payment_method')->nullable(); // cash, cheque, bank_transfer, online
            $table->string('status')->default('unpaid'); // unpaid, partial, paid
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->index('student_id');
            $table->index('status');
            $table->index('month');

            $table->foreign('school_id')
                ->references('id')
                ->on('schools')
                ->onDelete('cascade');

            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');

            $table->foreign('hostel_room_id')
                ->references('id')
                ->on('hostel_rooms')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_fees');
    }
};
