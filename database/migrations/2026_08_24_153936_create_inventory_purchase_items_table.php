<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_purchase_items', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('school_id');

            $table->unsignedBigInteger(
                'inventory_purchase_id'
            );

            $table->unsignedBigInteger(
                'inventory_item_id'
            );

            $table->decimal(
                'quantity',
                12,
                2
            );

            $table->decimal(
                'unit_price',
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
                'total',
                12,
                2
            )->default(0);

            $table->timestamps();

            $table->index('school_id');

            $table->index(
                'inventory_purchase_id'
            );

            $table->index(
                'inventory_item_id'
            );

            $table->foreign('school_id')
                ->references('id')
                ->on('schools')
                ->cascadeOnDelete();

            $table->foreign('inventory_purchase_id')
                ->references('id')
                ->on('inventory_purchases')
                ->cascadeOnDelete();

            $table->foreign('inventory_item_id')
                ->references('id')
                ->on('inventory_items')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'inventory_purchase_items'
        );
    }
};