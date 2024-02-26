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
        Schema::create('element_types_props', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("element_type_id");
            $table->foreign('element_type_id')->references('id')->on('element_types')->onDelete('cascade');
            $table->uuid("element_prop_id");
            $table->foreign('element_prop_id')->references('id')->on('element_props')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_types_props');
    }
};
