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
        Schema::create('pages_sections', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("section_id");
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->uuid("page_id");
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
            $table->integer("sequence_number");
            $table->string("section_link_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages_sections');
    }
};
