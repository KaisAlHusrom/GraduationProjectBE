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
        Schema::table('payment_plan_features', function (Blueprint $table) {
            $table->uuid("payment_plan_id")->nullable();
            $table->foreign('payment_plan_id')->references('id')->on('payment_plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_plans_features', function (Blueprint $table) {
            //
        });
    }
};
