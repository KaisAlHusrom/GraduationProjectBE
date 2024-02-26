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
        Schema::create('style_props', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("category_id");
            $table->foreign('category_id')->references('id')->on('style_props_categories')->onDelete('cascade');
            $table->string("style_prop_normal_name");
            $table->string("style_prop_css_name");
            $table->string("style_prop_image")->nullable();
            $table->string("style_prop_description");
            $table->string("style_prop_value_type");
            $table->boolean("is_section");
            $table->boolean("is_component");
            $table->boolean("is_element");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('style_props');
    }
};
