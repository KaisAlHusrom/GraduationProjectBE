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
        Schema::create('elements_props_values', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("component_element_id");
            $table->foreign('component_element_id')->references('id')->on('components_elements')->onDelete('cascade');
            $table->uuid("element_type_prop_id");
            $table->foreign('element_type_prop_id')->references('id')->on('element_types_props')->onDelete('cascade');
            $table->string("element_prop_value");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elements_props_values');
    }
};
