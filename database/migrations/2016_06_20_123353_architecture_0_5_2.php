<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Architecture052 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add deleted_at column to tags, users and countries tables
        foreach (['countries', 'tags', 'users'] as $tableName)
        {
            Schema::table($tableName, function(Blueprint $table)
    		{
                $table->softDeletes();
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
        // Remove deleted_at column from tags, users and countries tables
        foreach (['countries', 'tags', 'users'] as $tableName)
        {
            Schema::table($tableName, function(Blueprint $table)
    		{
                // $table->softDeletes();
            });
        }
    }
}
