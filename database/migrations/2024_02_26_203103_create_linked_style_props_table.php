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
        Schema::create('linked_style_props', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("section_id")->nullable();
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->uuid("component_id")->nullable();
            $table->foreign('component_id')->references('id')->on('components')->onDelete('cascade');
            $table->uuid("element_id")->nullable();
            $table->foreign('element_id')->references('id')->on('elements')->onDelete('cascade');
            $table->uuid("style_status_id")->nullable();
            $table->foreign('style_status_id')->references('id')->on('style_statuses')->onDelete('cascade');
            $table->uuid("style_responsive_breakpoint_id")->nullable();
            $table->foreign('style_responsive_breakpoint_id')->references('id')->on('style_responsive_breakpoints')->onDelete('cascade');
            $table->uuid("style_prop_id");
            $table->foreign('style_prop_id')->references('id')->on('style_props')->onDelete('cascade');
            $table->string("style_prop_value");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linked_style_props');
    }
};
