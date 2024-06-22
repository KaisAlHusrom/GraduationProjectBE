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
        Schema::create('designed_elements_prop_values', function (Blueprint $table) {
            $table->uuid("id")->primary()->default(DB::raw('UUID()'));
            $table->uuid("design_id");
            $table->uuid("element_prop_id");
            $table->string("design_prop_value");
            $table->timestamps();

            $table->foreign('design_id')->references('id')->on('designs')->onDelete('cascade');
            $table->foreign('element_prop_id')->references('id')->on('element_props')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designed_elements_prop_values');
    }
};
