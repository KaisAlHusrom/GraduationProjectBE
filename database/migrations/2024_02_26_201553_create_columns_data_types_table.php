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
        Schema::create('columns_data_types', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("column_id");
            $table->foreign('column_id')->references('id')->on('table_columns')->onDelete('cascade');
            $table->uuid("data_type_id");
            $table->foreign('data_type_id')->references('id')->on('data_types')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('columns_data_types');
    }
};
