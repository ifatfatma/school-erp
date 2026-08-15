<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incomes', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            $table->foreignId('income_category_id')
                ->constrained('income_categories')
                ->restrictOnDelete();

            $table->date('income_date');

            $table->string('income_no')
                ->nullable();

            $table->string('received_from');

            $table->string('payment_method')
                ->default('Cash');

            $table->decimal('amount', 12, 2)
                ->default(0);

            $table->string('reference_no')
                ->nullable();

            $table->text('description')
                ->nullable();

            $table->string('attachment')
                ->nullable();

            $table->enum('status', [
                'Received',
                'Pending',
                'Cancelled'
            ])->default('Received');

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

            $table->index([
                'school_id',
                'income_category_id'
            ]);

            $table->index([
                'school_id',
                'income_date'
            ]);

            $table->index([
                'school_id',
                'status'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};