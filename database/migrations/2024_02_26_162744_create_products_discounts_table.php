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
        Schema::create('products_discounts', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("user_id")->nullable(); // if the user is null, then the discount code will apply to all users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->uuid("product_id")->nullable(); // if the product is null then the discount will be applied on all products
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->string("discount_code")->nullable(); // if the code is null then the discount will be applied on all products without writing the discount_code
            $table->decimal("discount_percentage", 10, 2);
            $table->dateTime("discount_start_date");
            $table->dateTime("discount_end_date");
            $table->string("discount_title");
            $table->string("discount_occasion");
            $table->text("discount_description");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_discounts');
    }
};
