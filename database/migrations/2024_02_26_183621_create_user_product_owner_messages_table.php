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
        Schema::create('user_product_owner_messages', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("sender_id");
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->uuid("product_id");
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->text('message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_product_owner_messages');
    }
};
