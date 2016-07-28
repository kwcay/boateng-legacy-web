<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            // Internal ID.
            $table->increments('id')->length(6);

            // Language codes.
            $table->string('code', 7)->unique();
            $table->string('parent_code', 7)->nullable()->index();

            // Language details.
            $table->string('name', 100);                    // Name of language.
            $table->string('alt_names', 300)->nullable();   // Alternate names or spellings.
            $table->string('countries', 60)->nullable();    // Countries language is spoken in.

            //
            $table->text('params');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('languages');
    }
}
