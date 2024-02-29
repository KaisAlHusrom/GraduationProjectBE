<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnsDataTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('columns_data_types');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('columns_data_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('column_id')->constrained('table_columns')->onDelete('cascade');
            $table->foreignId('data_type_id')->constrained('data_types')->onDelete('cascade');
            $table->timestamps();
        });
    }
}
