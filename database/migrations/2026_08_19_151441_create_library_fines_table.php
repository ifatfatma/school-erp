<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('library_fines', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('school_id')->nullable();

            $table->unsignedBigInteger('book_issue_id');
            $table->unsignedBigInteger('library_member_id');

            $table->enum('fine_type', [
                'Overdue',
                'Lost',
                'Damaged',
                'Other'
            ]);

            $table->date('fine_date');

            $table->decimal('amount', 10, 2)->default(0);

            $table->decimal('paid_amount', 10, 2)->default(0);

            $table->decimal('due_amount', 10, 2)->default(0);

            $table->enum('status', [
                'Pending',
                'Partially Paid',
                'Paid',
                'Cancelled'
            ])->default('Pending');

            $table->text('description')->nullable();
            $table->text('remarks')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->index('book_issue_id');
            $table->index('library_member_id');
            $table->index('fine_type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_fines');
    }
};
