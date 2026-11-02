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
        Schema::create('element_types', function (Blueprint $table) {
            $table->uuid("id")->primary()->default(DB::raw('UUID()'));;
            $table->string("element_type_name")->unique();
            $table->string("element_type_description");
            $table->boolean("is_child")->default(false);
            $table->uuid("parent_id")->nullable();
            $table->foreign('parent_id')->references('id')->on('element_types')->onDelete('restrict');
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
