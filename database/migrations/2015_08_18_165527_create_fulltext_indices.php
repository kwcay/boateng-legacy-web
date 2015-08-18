<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFulltextIndices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // We only need a single fulltext index for language names.
        DB::statement('CREATE FULLTEXT INDEX idx_name ON languages (name, alt_names)');

        // We want ot be able to search the title, alt_titles, data and tags
        // columns and assign a weight to each column.
		DB::statement('CREATE FULLTEXT INDEX idx_title ON definitions (title, alt_titles)');
		DB::statement('CREATE FULLTEXT INDEX idx_data ON definitions (data)');
		DB::statement('CREATE FULLTEXT INDEX idx_tags ON definitions (tags)');

        // We only need a single fulltext index for translations.
		DB::statement('CREATE FULLTEXT INDEX idx_translation ON translations (translation, literal, meaning)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // TODO: how to drop a fulltext index.
    }
}
