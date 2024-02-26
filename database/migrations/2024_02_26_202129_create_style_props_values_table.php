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
        Schema::create('style_props_values', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("style_prop_id");
            $table->foreign('style_prop_id')->references('id')->on('style_props')->onDelete('cascade');
            $table->string("style_prop_value_normal_name");
            $table->string("style_prop_value_css_name");
            $table->string("style_prop_value_image")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('style_props_values');
    }
};
