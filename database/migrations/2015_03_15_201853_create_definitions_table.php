<?php

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
            $table->engine = 'InnoDB';

            // Internal ID.
			$table->increments('id')->length(9)->unsigned();
            $table->primary('id');

            // Definition details.
            $table->string('title', 400);                   // e.g. word, title of a poem, etc.
            $table->text('extra_data')->nullable();         // Alternate spellings, poem, etc.
            $table->tinyInteger('type')->unsigned();        // Data type.
            $table->string('source', 40)->nullable();       // Data source.
            $table->text('tags')->nullable();               // Descriptive tags.
            $table->string('langauage');                    // TODO

            $table->tinyInteger('state');
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
		Schema::drop('definitions');
	}
}

