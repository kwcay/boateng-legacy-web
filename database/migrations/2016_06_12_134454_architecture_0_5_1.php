<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Architecture051 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Remove "unique" FK restraint on transliteration column of alphabets table.
        Schema::hasTable('alphabets') ?
            DB::statement('ALTER TABLE alphabets DROP INDEX idx_transliteration') : null;

        // Remove reference column from definition_titles table
        Schema::table('definition_titles', function (Blueprint $table) {
            $table->dropColumn('reference');
        });

        // Add related_definitions column to definitions table
        Schema::table('definitions', function (Blueprint $table) {
            $table->text('related_definitions');
        });

        // Create references table.
        Schema::create('references', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');

            $table->string('type', 20);
            $table->text('data');
            $table->string('string', 400);
        });
        DB::statement('CREATE FULLTEXT INDEX idx_reference ON `references` (string)');

        // Create reference pivot table.
        Schema::create('referenceable', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->integer('reference_id')->unsigned();
            $table->foreign('reference_id')
                ->references('id')
                ->on('references')
                ->onDelete('cascade');

            $table->integer('referenceable_id')->unsigned();
            $table->string('referenceable_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop pivot table.
        Schema::hasTable('referenceable') ? Schema::drop('referenceable') : null;

        // Drop references table.
        Schema::hasTable('references') ? DB::statement('ALTER TABLE `references` DROP INDEX idx_reference') : null;
        Schema::hasTable('references') ? Schema::drop('references') : null;

        // Remove related_definitions column from definitions table
        Schema::table('definitions', function (Blueprint $table) {
            $table->dropColumn('related_definitions');
        });

        // Create reference column on definition_titles table
        Schema::table('definition_titles', function (Blueprint $table) {
            $table->string('reference');
        });

        // Create "unique" FK restraint on transliteration column of alphabets table.
        Schema::hasTable('alphabets') ?
            DB::statement('CREATE FULLTEXT INDEX idx_transliteration ON alphabets (transliteration)') :
            null;
    }
}
