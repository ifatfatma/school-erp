<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('school_id');

            $table->unsignedBigInteger('inventory_category_id');

            $table->unsignedBigInteger('inventory_unit_id');

            $table->unsignedBigInteger('inventory_supplier_id')
                ->nullable();

            $table->string('name');

            $table->string('sku')->nullable();

            $table->string('barcode')->nullable();

            $table->decimal(
                'opening_stock',
                12,
                2
            )->default(0);

            $table->decimal(
                'reorder_level',
                12,
                2
            )->default(0);

            $table->decimal(
                'cost_price',
                12,
                2
            )->default(0);

            $table->decimal(
                'sale_price',
                12,
                2
            )->default(0);

            $table->boolean('status')
                ->default(true);

            $table->timestamps();

            $table->softDeletes();

            $table->index('school_id');

            $table->index(
                'inventory_category_id'
            );

            $table->index(
                'inventory_unit_id'
            );

            $table->index(
                'inventory_supplier_id'
            );

            /*
            |--------------------------------------------------------------------------
            | Foreign Keys
            |--------------------------------------------------------------------------
            */

            $table->foreign('school_id')
                ->references('id')
                ->on('schools')
                ->cascadeOnDelete();

            $table->foreign('inventory_category_id')
                ->references('id')
                ->on('inventory_categories')
                ->restrictOnDelete();

            $table->foreign('inventory_unit_id')
                ->references('id')
                ->on('inventory_units')
                ->restrictOnDelete();

            $table->foreign('inventory_supplier_id')
                ->references('id')
                ->on('inventory_suppliers')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | SKU unique per school
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'school_id',
                'sku'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'inventory_items'
        );
    }
};