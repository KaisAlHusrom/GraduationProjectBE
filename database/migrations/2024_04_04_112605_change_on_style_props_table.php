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
        Schema::table('style_props', function (Blueprint $table) {
            $table->boolean("is_child")->default(false);
            $table->uuid("parent_id")->nullable();
            $table->foreign('parent_id')->references('id')->on('style_props')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
