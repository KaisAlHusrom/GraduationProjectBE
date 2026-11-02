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
        Schema::table('style_props', function (Blueprint $table) {
            $table->boolean('is_section')->default(false)->change();
            $table->boolean('is_component')->default(false)->change();
            $table->boolean('is_element')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('style_props', function (Blueprint $table) {
            //
        });
    }
};
