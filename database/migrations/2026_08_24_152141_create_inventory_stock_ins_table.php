<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_stock_ins', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('school_id');

            $table->unsignedBigInteger('inventory_supplier_id')
                ->nullable();

            $table->string('stock_in_no');

            $table->date('stock_in_date');

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

            $table->string('status')
                ->default('Received');

            $table->text('remarks')
                ->nullable();

            $table->unsignedBigInteger('created_by')
                ->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index('school_id');

            $table->index(
                'inventory_supplier_id'
            );

            $table->index(
                'stock_in_date'
            );

            $table->foreign('school_id')
                ->references('id')
                ->on('schools')
                ->cascadeOnDelete();

            $table->foreign('inventory_supplier_id')
                ->references('id')
                ->on('inventory_suppliers')
                ->nullOnDelete();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->unique([
                'school_id',
                'stock_in_no'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'inventory_stock_ins'
        );
    }
};