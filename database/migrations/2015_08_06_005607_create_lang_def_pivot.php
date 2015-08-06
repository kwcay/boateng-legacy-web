<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLangDefPivot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('definition_language', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';

            // Language ID
            $table->integer('language_id')->length(6)->unsigned();

            // Definition ID
            $table->integer('definition_id')->length(9)->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('definition_language');
    }
}
