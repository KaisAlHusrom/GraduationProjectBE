<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('components_elements', function (Blueprint $table) {
            $table->uuid("id")->primary()->default(DB::raw('UUID()'));;
            $table->uuid("element_id");
            $table->foreign('element_id')->references('id')->on('elements')->onDelete('cascade');
            $table->uuid("component_id");
            $table->foreign('component_id')->references('id')->on('components')->onDelete('cascade');
            $table->string("element_content")->nullable();
            $table->integer("sequence_number");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('components_elements');
    }
};
