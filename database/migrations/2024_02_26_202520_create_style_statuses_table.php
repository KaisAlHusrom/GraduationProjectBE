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
        Schema::create('style_statuses', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("style_status_normal_name");
            $table->string("style_status_css_name");
            $table->string("style_status_image")->nullable();
            $table->text("style_status_description")->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('style_statuses');
    }
};
