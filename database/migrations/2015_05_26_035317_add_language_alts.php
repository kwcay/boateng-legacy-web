<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLanguageAlts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('languages', function(Blueprint $table)
		{
			// Add "alt" column.
            $table->string('alt', 300);

            // Add "state" column.
            $table->tinyInteger('state');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('languages', function(Blueprint $table)
		{
			// Drop columns
            $table->dropColumn('alt');
            $table->dropColumn('state');
		});
	}

}
