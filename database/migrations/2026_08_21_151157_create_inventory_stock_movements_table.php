<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_stock_movements', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('school_id');

            $table->unsignedBigInteger('inventory_item_id');

            $table->string('movement_no');

            /*
            |--------------------------------------------------------------------------
            | IN / OUT / ADJUSTMENT / TRANSFER_IN / TRANSFER_OUT
            |--------------------------------------------------------------------------
            */

            $table->string('movement_type');

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
                'total_amount',
                12,
                2
            )->default(0);

            /*
            |--------------------------------------------------------------------------
            | Stock before / after movement
            |--------------------------------------------------------------------------
            */

            $table->decimal(
                'stock_before',
                12,
                2
            )->default(0);

            $table->decimal(
                'stock_after',
                12,
                2
            )->default(0);

            $table->date('movement_date');

            $table->string('reference_type')
                ->nullable();

            $table->unsignedBigInteger('reference_id')
                ->nullable();

            $table->text('remarks')
                ->nullable();

            $table->unsignedBigInteger('created_by')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('school_id');

            $table->index(
                'inventory_item_id'
            );

            $table->index(
                'movement_type'
            );

            $table->index(
                'movement_date'
            );

            $table->index(
                'reference_id'
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

            $table->foreign('inventory_item_id')
                ->references('id')
                ->on('inventory_items')
                ->restrictOnDelete();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Movement Number
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'school_id',
                'movement_no'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'inventory_stock_movements'
        );
    }
};