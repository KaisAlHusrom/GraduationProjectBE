<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('free_trial_expiration_date')->nullable();
        });

        // Update existing records with the default value
        DB::statement('UPDATE users SET free_trial_expiration_date = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 7 DAY) WHERE free_trial_expiration_date IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
