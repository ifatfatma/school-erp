<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_suppliers', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('school_id');

            $table->string('name');

            $table->string('code', 100)->nullable();

            $table->string('contact_person')->nullable();

            $table->string('phone', 50)->nullable();

            $table->string('email')->nullable();

            $table->text('address')->nullable();

            $table->string('city')->nullable();

            $table->string('state')->nullable();

            $table->string('country')->nullable();

            $table->string('tax_number', 100)->nullable();

            $table->text('notes')->nullable();

            $table->boolean('status')->default(true);

            $table->timestamps();

            $table->softDeletes();

            $table->index('school_id');

            $table->index([
                'school_id',
                'name'
            ]);

            $table->index([
                'school_id',
                'code'
            ]);

            $table->foreign('school_id')
                ->references('id')
                ->on('schools')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_suppliers');
    }
};