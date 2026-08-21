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
        Schema::create('lost_damaged_books', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('school_id')->nullable();

            $table->unsignedBigInteger('book_issue_id');
            $table->unsignedBigInteger('book_copy_id');
            $table->unsignedBigInteger('library_member_id');

            $table->enum('type', ['Lost', 'Damaged']);

            $table->date('reported_date');

            $table->decimal('book_value', 10, 2)->default(0);
            $table->decimal('fine_amount', 10, 2)->default(0);

            $table->text('description')->nullable();

            $table->enum('status', [
                'Reported',
                'Resolved',
                'Cancelled'
            ])->default('Reported');

            $table->unsignedBigInteger('reported_by')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();

            $table->timestamp('resolved_at')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->index('book_issue_id');
            $table->index('book_copy_id');
            $table->index('library_member_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lost_damaged_books');
    }
};
