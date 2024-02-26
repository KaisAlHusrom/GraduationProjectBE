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
        Schema::create('tables_relationships', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("main_column_id");
            $table->foreign('main_column_id')->references('id')->on('table_columns')->onDelete('cascade');
            $table->uuid("linked_column_id");
            $table->foreign('linked_column_id')->references('id')->on('table_columns')->onDelete('cascade');
            $table->uuid("relation_type_id");
            $table->foreign('relation_type_id')->references('id')->on('relation_types')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tables_relationships');
    }
};
