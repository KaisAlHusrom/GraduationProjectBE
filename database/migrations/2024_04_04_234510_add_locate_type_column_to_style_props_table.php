<?php

use App\Enums\LocateTypes;
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
            $table->string('locateTypes')->default(LocateTypes::withoutDirections);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('style_props', function (Blueprint $table) {
            //
        });
    }
};
