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
        Schema::create('table_columns', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("table_id");
            $table->foreign('table_id')->references('id')->on('database_tables')->onDelete('cascade');
            $table->string('column_name');
            $table->boolean("is_pk")->default(false);
            $table->boolean("is_fk")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_columns');
    }
};
