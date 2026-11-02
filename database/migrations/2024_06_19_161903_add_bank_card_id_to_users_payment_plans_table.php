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
        Schema::table('users_payment_plans', function (Blueprint $table) {
            $table->uuid('bank_card_id')->nullable();
            $table->foreign('bank_card_id')
                ->references('id')
                ->on('bank_cards')
                ->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_payment_plans', function (Blueprint $table) {
            //
        });
    }
};
