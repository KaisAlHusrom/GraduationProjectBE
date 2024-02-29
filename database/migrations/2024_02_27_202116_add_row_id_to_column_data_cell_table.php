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
        Schema::table('column_data_cells', function (Blueprint $table) {
            $table->uuid("row_id");
            $table->foreign('row_id')->references('id')->on('table_rows')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('column_data_cells', function (Blueprint $table) {
            //
        });
    }
};
