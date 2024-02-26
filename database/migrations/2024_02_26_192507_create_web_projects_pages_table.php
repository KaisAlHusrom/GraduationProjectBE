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
        Schema::create('web_projects_pages', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("web_project_id");
            $table->foreign('web_project_id')->references('id')->on('web_projects')->onDelete('cascade');
            $table->uuid("page_id");
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
            $table->string("page_router");
            $table->string("page_protector_route_user_column"); //TODO: maybe remove later
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_projects_pages');
    }
};
