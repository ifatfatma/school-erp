<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_purchases', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('school_id');

            $table->unsignedBigInteger('inventory_supplier_id')
                ->nullable();

            $table->string('purchase_no');

            $table->date('purchase_date');

            $table->string('invoice_no')
                ->nullable();

            $table->decimal(
                'subtotal',
                12,
                2
            )->default(0);

            $table->decimal(
                'discount',
                12,
                2
            )->default(0);

            $table->decimal(
                'tax',
                12,
                2
            )->default(0);

            $table->decimal(
                'total_amount',
                12,
                2
            )->default(0);

            $table->text('notes')
                ->nullable();

            $table->string('status')
                ->default('Received');

            $table->timestamps();

            $table->softDeletes();

            $table->index('school_id');

            $table->index(
                'inventory_supplier_id'
            );

            $table->unique([
                'school_id',
                'purchase_no'
            ]);

            $table->foreign('school_id')
                ->references('id')
                ->on('schools')
                ->cascadeOnDelete();

            $table->foreign('inventory_supplier_id')
                ->references('id')
                ->on('inventory_suppliers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'inventory_purchases'
        );
    }
};