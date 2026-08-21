<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_fines', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('school_id');

            $table->unsignedBigInteger('library_member_id');

            $table->unsignedBigInteger('book_issue_id')
                ->nullable();

            $table->unsignedBigInteger('book_id')
                ->nullable();

            $table->unsignedBigInteger('book_copy_id')
                ->nullable();

            $table->unsignedBigInteger('lost_damaged_book_id')
                ->nullable();

            $table->string('fine_no');

            $table->date('fine_date');

            $table->enum('fine_type', [
                'Overdue',
                'Lost',
                'Damaged',
                'Other',
            ]);

            $table->text('description')
                ->nullable();

            $table->integer('overdue_days')
                ->default(0);

            $table->decimal(
                'fine_per_day',
                12,
                2
            )->default(0);

            $table->decimal(
                'fine_amount',
                12,
                2
            )->default(0);

            $table->decimal(
                'waiver_amount',
                12,
                2
            )->default(0);

            $table->decimal(
                'payable_amount',
                12,
                2
            )->default(0);

            $table->enum('status', [
                'Pending',
                'Partially Paid',
                'Paid',
                'Waived',
            ])->default('Pending');

            $table->text('remarks')
                ->nullable();

            $table->unsignedBigInteger('created_by')
                ->nullable();

            $table->timestamps();

            $table->softDeletes();


            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'school_id',
                'fine_no',
            ]);

            $table->index('library_member_id');
            $table->index('book_issue_id');
            $table->index('book_id');
            $table->index('book_copy_id');
            $table->index('lost_damaged_book_id');
            $table->index('created_by');


            /*
            |--------------------------------------------------------------------------
            | Foreign Keys
            |--------------------------------------------------------------------------
            */

            $table->foreign('school_id')
                ->references('id')
                ->on('schools')
                ->cascadeOnDelete();

            $table->foreign('library_member_id')
                ->references('id')
                ->on('library_members')
                ->restrictOnDelete();

            $table->foreign('book_issue_id')
                ->references('id')
                ->on('book_issues')
                ->nullOnDelete();

            $table->foreign('book_id')
                ->references('id')
                ->on('books')
                ->nullOnDelete();

            $table->foreign('book_copy_id')
                ->references('id')
                ->on('book_copies')
                ->nullOnDelete();

            $table->foreign('lost_damaged_book_id')
                ->references('id')
                ->on('lost_damaged_books')
                ->nullOnDelete();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_fines');
    }
};