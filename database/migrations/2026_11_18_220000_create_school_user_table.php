<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('school_user')) {
            Schema::create('school_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('role', 50)->default('admin');
                $table->boolean('is_primary')->default(false);
                $table->timestamps();

                $table->unique(['user_id', 'school_id']);
                $table->index(['user_id', 'is_primary']);
            });

            // Backfill existing users with their current school_id
            $now = now();
            $users = DB::table('users')->whereNotNull('school_id')->get(['id', 'school_id', 'user_type', 'created_at']);
            $insertRows = [];

            foreach ($users as $u) {
                $insertRows[] = [
                    'school_id' => $u->school_id,
                    'user_id' => $u->id,
                    'role' => $u->user_type ?? 'admin',
                    'is_primary' => true,
                    'created_at' => $u->created_at ?? $now,
                    'updated_at' => $now,
                ];
            }

            if (!empty($insertRows)) {
                // Insert in chunks to avoid any query parameter size limits
                foreach (array_chunk($insertRows, 200) as $chunk) {
                    DB::table('school_user')->insertOrIgnore($chunk);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_user');
    }
};
