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
        Schema::table('table_columns', function (Blueprint $table) {
            $table->uuid("data_type_id");
            $table->foreign('data_type_id')->references('id')->on('data_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('table_columns', function (Blueprint $table) {
            $table->dropForeign(['data_type_id']);
            $table->dropColumn('data_type_id');
        });
    }
};
