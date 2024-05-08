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
        Schema::table('elements', function (Blueprint $table) {
                // Drop the existing foreign key constraint
                $table->dropForeign(['children_id']);

                // Remove the existing column
                $table->dropColumn('children_id');

                // Add the new column 'parent_id'
                $table->uuid("parent_id")->nullable();

                // Add foreign key constraint for parent_id
                $table->foreign('parent_id')->references('id')->on('elements')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parent_id_on_elements', function (Blueprint $table) {
            //
        });
    }
};
