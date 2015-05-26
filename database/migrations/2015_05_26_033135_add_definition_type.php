<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefinitionType extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('definitions', function(Blueprint $table)
		{
			// Add "type" column.
            $table->tinyInteger('type')->unsigned();

            // Add "alt" column.
            $table->string('alt', 200);

            // Remove "state" column.
            $table->dropColumn('state');

            // Other changes.
            $table->softDeletes();
		});


        Schema::table('definitions', function(Blueprint $table)
        {
            // Add "state" column, without the length restriction.
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
        Schema::table('definitions', function(Blueprint $table)
        {
            // Remove "type" column.
            $table->dropColumn('type');

            // Remove "alt" column
            $table->dropColumn('alt');

            // Reinstate "state" column.
            $table->dropColumn('state');

            // Un-softDelete.
            $table->dropSoftDeletes();
        });

        Schema::table('definitions', function(Blueprint $table)
        {
            // Add "state" column with length restriction.
            $table->tinyInteger('state')->length(1)->unsigned();
        });
	}

}
