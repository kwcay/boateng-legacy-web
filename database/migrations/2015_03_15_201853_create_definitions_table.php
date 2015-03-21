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
            
			$table->increments('id')->length(9)->unsigned();
            $table->string('word', 200);
            $table->string('language', 70);
            $table->text('translation');
            $table->text('meaning');
            $table->string('source', 40);
            $table->tinyInteger('state')->length(1)->unsigned();
            $table->text('params');
			$table->timestamps();
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
