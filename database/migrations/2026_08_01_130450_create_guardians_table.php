<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('guardians', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('guardian_type', [
                'Father',
                'Mother',
                'Grandfather',
                'Grandmother',
                'Uncle',
                'Aunt',
                'Brother',
                'Sister',
                'Legal Guardian',
                'Other',
            ])->default('Father');

            $table->string('first_name');
            $table->string('last_name')->nullable();

            $table->enum('gender', [
                'Male',
                'Female',
                'Other',
            ])->nullable();

            $table->date('dob')->nullable();

            $table->string('mobile', 20);
            $table->string('alternate_mobile', 20)->nullable();

            $table->string('email')->nullable();

            $table->string('occupation')->nullable();

            $table->decimal('annual_income', 12, 2)
                ->nullable()
                ->default(0);

            $table->string('education')->nullable();

            $table->string('aadhaar_no', 20)->nullable();

            $table->string('pan_no', 20)->nullable();

            $table->string('relation')->nullable();

            $table->text('address')->nullable();

            $table->string('city')->nullable();

            $table->string('state')->nullable();

            $table->string('country')->nullable();

            $table->string('pincode', 10)->nullable();

            $table->string('photo')->nullable();

            $table->boolean('status')
                ->default(true);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->softDeletes();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index(['school_id', 'status']);

            $table->index(['guardian_type']);

            $table->index(['mobile']);

            $table->index(['email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};