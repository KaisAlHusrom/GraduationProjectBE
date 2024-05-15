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
        Schema::table('element_types', function (Blueprint $table) {
            $table->uuid("category_id")->nullable();
            $table->foreign('category_id')->references('id')->on('element_type_categories')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('element_type', function (Blueprint $table) {
            //
        });
    }
};
