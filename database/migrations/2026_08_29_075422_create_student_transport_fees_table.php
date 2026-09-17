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
        Schema::create('student_transport_fees', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('transport_assignment_id');
            $table->unsignedBigInteger('transport_route_id');
            $table->unsignedBigInteger('transport_route_stop_id')->nullable();

            $table->decimal('amount', 10, 2)->default(0.00);
            $table->decimal('discount', 10, 2)->default(0.00);

            $table->date('start_date');
            $table->date('end_date')->nullable();

            $table->string('billing_cycle', 50)->default('Monthly');

            $table->enum('status', ['Active', 'Inactive'])
                ->default('Active');

            $table->text('remarks')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('school_id', 'idx_stf_school_id');
            $table->index('student_id', 'idx_stf_student_id');
            $table->index(
                'transport_assignment_id',
                'idx_stf_assignment_id'
            );
            $table->index(
                'transport_route_id',
                'idx_stf_route_id'
            );
            $table->index(
                'transport_route_stop_id',
                'idx_stf_stop_id'
            );
            $table->index('status', 'idx_stf_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_transport_fees');
    }
};