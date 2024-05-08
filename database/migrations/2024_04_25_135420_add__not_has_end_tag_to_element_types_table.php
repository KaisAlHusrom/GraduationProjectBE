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
        Schema::table('element_types', function (Blueprint $table) {
            $table->boolean("not_has_end_tag")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('element_types', function (Blueprint $table) {
            $table->dropColumn("not_has_end_tag");
        });
    }
};
