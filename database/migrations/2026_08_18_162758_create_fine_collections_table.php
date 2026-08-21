<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fine_collections', function (Blueprint $table) {

    $table->id();

    $table->unsignedBigInteger('school_id');
    $table->unsignedBigInteger('library_fine_id');
    $table->unsignedBigInteger('library_member_id');

    $table->string('collection_no');
    $table->string('receipt_no')->nullable();

    $table->date('collection_date');

    $table->decimal('amount', 12, 2);

    $table->enum('payment_mode', [
        'Cash',
        'Card',
        'Bank Transfer',
        'UPI',
        'Cheque',
        'Online',
    ])->default('Cash');

    $table->string('transaction_no')->nullable();

    $table->unsignedBigInteger('collected_by')->nullable();

    $table->text('remarks')->nullable();

    $table->timestamps();
    $table->softDeletes();


    /*
    |--------------------------------------------------------------------------
    | Foreign Keys
    |--------------------------------------------------------------------------
    */

    $table->foreign('school_id')
        ->references('id')
        ->on('schools')
        ->cascadeOnDelete();

    $table->foreign('library_fine_id')
        ->references('id')
        ->on('library_fines')
        ->restrictOnDelete();

    $table->foreign('library_member_id')
        ->references('id')
        ->on('library_members')
        ->restrictOnDelete();

    $table->foreign('collected_by')
        ->references('id')
        ->on('users')
        ->nullOnDelete();


    /*
    |--------------------------------------------------------------------------
    | Indexes
    |--------------------------------------------------------------------------
    */

    $table->unique([
        'school_id',
        'collection_no'
    ]);

    $table->index([
        'school_id',
        'library_fine_id'
    ]);

    $table->index([
        'school_id',
        'library_member_id'
    ]);

    $table->index([
        'school_id',
        'collection_date'
    ]);

    $table->index([
        'school_id',
        'payment_mode'
    ]);

});
    }


    public function down(): void
    {
        Schema::dropIfExists('fine_collections');
    }
};