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
            $table->string('name', 100);
            $table->string('alt_names', 300);
            $table->string('countries', 60);
            $table->text('desc');
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
		Schema::drop('languages');
	}

}
