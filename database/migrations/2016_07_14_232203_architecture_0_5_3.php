<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Architecture053 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add language column to data and tags tables.
        foreach (['data', 'tags'] as $tableName)
        {
            Schema::table($tableName, function(Blueprint $table)
    		{
                $table->string('language', 3);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove language column from data and tags tables.
        foreach (['data', 'tags'] as $tableName)
        {
            Schema::table($tableName, function(Blueprint $table)
    		{
                $table->dropColumn('language');
            });
        }
    }
}
