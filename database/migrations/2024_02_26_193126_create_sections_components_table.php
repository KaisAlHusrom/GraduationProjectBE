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
        Schema::create('sections_components', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("section_id");
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->uuid("component_id");
            $table->foreign('component_id')->references('id')->on('components')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections_components');
    }
};
