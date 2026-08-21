<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lost_damaged_books', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | School
            |--------------------------------------------------------------------------
            */

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Book Issue
            |--------------------------------------------------------------------------
            */

            $table->foreignId('book_issue_id')
                ->nullable()
                ->constrained('book_issues')
                ->nullOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Library Member
            |--------------------------------------------------------------------------
            */

            $table->foreignId('library_member_id')
                ->constrained('library_members')
                ->restrictOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Book
            |--------------------------------------------------------------------------
            */

            $table->foreignId('book_id')
                ->constrained('books')
                ->restrictOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Book Copy
            |--------------------------------------------------------------------------
            */

            $table->foreignId('book_copy_id')
                ->constrained('book_copies')
                ->restrictOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Report Information
            |--------------------------------------------------------------------------
            */

            $table->string('report_no');

            $table->date('report_date');


            /*
            |--------------------------------------------------------------------------
            | Type
            |--------------------------------------------------------------------------
            */

            $table->enum('type', [
                'Lost',
                'Damaged',
            ]);


            /*
            |--------------------------------------------------------------------------
            | Damage / Loss Details
            |--------------------------------------------------------------------------
            */

            $table->text('description')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Book Value
            |--------------------------------------------------------------------------
            */

            $table->decimal(
                'book_value',
                12,
                2
            )->default(0);


            /*
            |--------------------------------------------------------------------------
            | Charge
            |--------------------------------------------------------------------------
            */

            $table->decimal(
                'charge_amount',
                12,
                2
            )->default(0);


            /*
            |--------------------------------------------------------------------------
            | Charge Status
            |--------------------------------------------------------------------------
            */

            $table->enum('charge_status', [
                'Pending',
                'Paid',
                'Waived',
            ])->default('Pending');


            /*
            |--------------------------------------------------------------------------
            | Resolution
            |--------------------------------------------------------------------------
            */

            $table->enum('resolution', [
                'Pending',
                'Replacement',
                'Payment',
                'Waived',
            ])->default('Pending');


            /*
            |--------------------------------------------------------------------------
            | Resolved Date
            |--------------------------------------------------------------------------
            */

            $table->date('resolved_date')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Remarks
            |--------------------------------------------------------------------------
            */

            $table->text('remarks')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Reported By
            |--------------------------------------------------------------------------
            */

            $table->foreignId('reported_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            $table->softDeletes();


            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'school_id',
                'report_no',
            ]);

            $table->index([
                'school_id',
                'library_member_id',
            ]);

            $table->index([
                'school_id',
                'book_id',
            ]);

            $table->index([
                'school_id',
                'book_copy_id',
            ]);

            $table->index([
                'school_id',
                'type',
            ]);

            $table->index([
                'school_id',
                'charge_status',
            ]);

            $table->index([
                'school_id',
                'report_date',
            ]);

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('lost_damaged_books');
    }
};