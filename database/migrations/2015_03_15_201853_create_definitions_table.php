<?php

use DB;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefinitionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('definitions', function(Blueprint $table)
		{
            $table->engine = 'MyISAM';

            // Internal ID.
			$table->increments('id')->length(9)->primary();

            // Definition details.
            $table->string('title', 400);                   // e.g. word, title of a poem, etc.
            $table->text('extra_data')->nullable();         // Alternate spellings, poem, etc.
            $table->tinyInteger('type')->unsigned();        // Data type.
            $table->text('tags')->nullable();               // Descriptive tags.

            $table->tinyInteger('state');
            $table->text('params');
			$table->timestamps();
            $table->softDeletes();
		});

        DB::statement('ALTER TABLE definitions ADD FULLTEXT idx_fulltext(title, extra_data, tags)');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('definitions');
	}
}

