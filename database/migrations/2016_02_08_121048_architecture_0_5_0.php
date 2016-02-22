<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Architecture050 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        return;

        // Language scripts.
        Schema::table('scripts', function(Blueprint $table)
        {

        });

        // Definitions.
        Schema::table('definitions', function(Blueprint $table) {});

        // Translations.
        Schema::table('translations', function(Blueprint $table) {});

        // Tags.
        Schema::table('tags', function(Blueprint $table) {});

        // Sentences.
        Schema::hasTable('sentences') ? Schema::drop('sentences') : null;
        Schema::hasTable('definition_sentence') ? Schema::drop('definition_sentence') : null;

        // Media.
        Schema::table('media', function(Blueprint $table) {});

        // Data.
        Schema::table('data', function(Blueprint $table) {});

        // Cultures.
        Schema::table('cultures', function(Blueprint $table) {});

        // Countries.
        Schema::table('countries', function(Blueprint $table) {});

        // Users.
        Schema::table('users', function(Blueprint $table) {});

        // Roles & permissions.
        // ...
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        return;
        
        //
    }
}
