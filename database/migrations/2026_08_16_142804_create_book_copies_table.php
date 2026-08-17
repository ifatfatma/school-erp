<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_copies', function (Blueprint $table) {

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
            | Book
            |--------------------------------------------------------------------------
            */

            $table->foreignId('book_id')
                ->constrained('books')
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Copy Information
            |--------------------------------------------------------------------------
            */

            $table->string('accession_no');

            $table->string('barcode')
                ->nullable();

            $table->string('copy_no')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Location
            |--------------------------------------------------------------------------
            */

            $table->string('shelf_no')
                ->nullable();

            $table->string('rack_no')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Purchase Information
            |--------------------------------------------------------------------------
            */

            $table->date('purchase_date')
                ->nullable();

            $table->decimal('purchase_price', 12, 2)
                ->default(0);


            /*
            |--------------------------------------------------------------------------
            | Condition
            |--------------------------------------------------------------------------
            */

            $table->enum('condition', [
                'New',
                'Good',
                'Fair',
                'Damaged',
                'Lost',
            ])->default('New');


            /*
            |--------------------------------------------------------------------------
            | Availability
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'Available',
                'Issued',
                'Reserved',
                'Lost',
                'Damaged',
                'Maintenance',
            ])->default('Available');


            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

            $table->text('notes')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Audit
            |--------------------------------------------------------------------------
            */

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();


            $table->timestamps();

            $table->softDeletes();


            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index([
                'school_id',
                'book_id',
            ]);

            $table->index([
                'school_id',
                'status',
            ]);

            $table->index([
                'school_id',
                'condition',
            ]);


            /*
            |--------------------------------------------------------------------------
            | Unique identifiers within school
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'school_id',
                'accession_no',
            ]);

            $table->unique([
                'school_id',
                'barcode',
            ]);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('book_copies');
    }
};