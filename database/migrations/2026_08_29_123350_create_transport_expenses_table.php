<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transport_expenses', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('school_id');

            $table->unsignedBigInteger('transport_vehicle_id')
                ->nullable();

            $table->unsignedBigInteger('transport_driver_id')
                ->nullable();

            $table->string('expense_no')
                ->nullable();

            $table->date('expense_date');

            $table->string('expense_type');

            $table->decimal('quantity', 12, 2)
                ->nullable();

            $table->string('unit')
                ->nullable();

            $table->decimal('rate', 12, 2)
                ->default(0);

            $table->decimal('amount', 12, 2)
                ->default(0);

            $table->string('vendor')
                ->nullable();

            $table->string('payment_method')
                ->nullable();

            $table->string('reference_no')
                ->nullable();

            $table->text('description')
                ->nullable();

            $table->text('remarks')
                ->nullable();

            $table->unsignedBigInteger('created_by')
                ->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index('school_id');
            $table->index('transport_vehicle_id');
            $table->index('transport_driver_id');
            $table->index('expense_date');
            $table->index('expense_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_expenses');
    }
};