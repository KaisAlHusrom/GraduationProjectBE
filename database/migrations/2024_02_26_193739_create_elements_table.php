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
            Schema::create('elements', function (Blueprint $table) {
                $table->uuid("id")->primary();
                $table->uuid("element_type_id");
                $table->foreign('element_type_id')->references('id')->on('element_types')->onDelete('restrict');
                $table->string("element_title");
                $table->text("element_description");
                $table->string("element_image")->nullable();
                $table->boolean("is_template")->default(false);
                $table->softDeletes();
                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('elements');
        }
    };
