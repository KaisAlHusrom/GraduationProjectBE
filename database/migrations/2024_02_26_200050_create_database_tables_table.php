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
        Schema::create('database_tables', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("database_id");
            $table->foreign('database_id')->references('id')->on('databases')->onDelete('cascade');
            $table->string('table_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('database_tables');
    }
};
