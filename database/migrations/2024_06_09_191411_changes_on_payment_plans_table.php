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
        Schema::table('payment_plans', function (Blueprint $table) {
            DB::statement('ALTER TABLE `payment_plans` CHANGE `payment_plan_price` `payment_plan_monthly_price` DECIMAL(10, 2)');
            $table->decimal("payment_plan_yearly_price", 10, 2);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
