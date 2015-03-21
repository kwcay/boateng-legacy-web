<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLanguagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('languages', function(Blueprint $table)
		{
            $table->engine = 'InnoDB';
            
			$table->increments('id')->length(6)->unsigned();
            $table->string('code', 7)->unique();
            $table->string('parent', 7);
            $table->string('name', 300);
            $table->string('countries', 60);
            $table->text('desc');
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
		Schema::drop('languages');
	}

}
