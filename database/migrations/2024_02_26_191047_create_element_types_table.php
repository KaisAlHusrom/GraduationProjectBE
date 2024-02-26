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
        Schema::create('element_types', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("element_type_name")->unique();
            $table->string("element_type_description");
            $table->string("is_child")->default(false);
            $table->uuid("children_id")->nullable();
            $table->foreign('children_id')->references('id')->on('element_types')->onDelete('restrict');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_types');
    }
};
