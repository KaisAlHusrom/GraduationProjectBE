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
        Schema::create('web_projects', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("user_id");
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string("project_title");
            $table->string("project_logo");
            $table->string("project_type");
            $table->string("project_job");
            $table->text("project_description");
            $table->boolean("is_template")->default(false);
            $table->boolean("is_own_project");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_projects');
    }
};
