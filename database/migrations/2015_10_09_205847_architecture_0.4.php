<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Architecture_0_4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop all existing tables except for password_resets.
        Schema::hasTable('users') ? Schema::drop('users') : null;
        Schema::hasTable('languages') ? Schema::drop('languages') : null;
        Schema::hasTable('definitions') ? Schema::drop('definitions') : null;
        Schema::hasTable('translations') ? Schema::drop('translations') : null;
        Schema::hasTable('definition_language') ? Schema::drop('definition_language') : null;

        // Languages.
        Schema::create('languages', function(Blueprint $table)
		{
            $table->engine = 'InnoDB';

			$table->increments('id');
            $table->string('code', 7)->unique();
            $table->string('parent_code', 7)->nullable()->index();
            $table->string('name', 100);
            $table->string('alt_names', 300)->nullable();
			$table->timestamps();
            $table->softDeletes();
		});

        // Language scripts.
        Schema::create('scripts', function(Blueprint $table)
		{
            $table->engine = 'InnoDB';

			$table->increments('id');
            $table->string('name', 100);
            $table->string('abbreviation', 10);
            $table->text('letters');
			$table->timestamps();
            $table->softDeletes();
		});

        // Definitions.
        Schema::create('definitions', function(Blueprint $table)
		{
            $table->engine = 'InnoDB';

			$table->increments('id');
            $table->string('title', 400);               	// e.g. word, title of a poem, etc.
            $table->string('alt_titles', 400)->nullable();	// Alternate spellings.
            $table->tinyInteger('type')->unsigned();		// Data type, e.g. word.
            $table->string('sub_type');	    				// Data sub-type, e.g. noun.
            $table->tinyInteger('state');
			$table->timestamps();
            $table->softDeletes();
		});

        // Translations.
        Schema::create('translations', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('definition_id')->unsigned();
            $table->foreign('definition_id')
                ->references('id')
                ->on('definitions')
                ->onDelete('cascade');
            $table->string('language', 3)->index(); // Main language (not expecting sub-languages here).
            $table->text('practical');              // Actual translation.
            $table->text('literal');                // Literal translation.
            $table->text('meaning');                // Elaboration on the meaning of the definition.
            $table->timestamps();
            $table->softDeletes();
        });

        // Tags.
        Schema::create('tags', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('title', 100);
            $table->timestamps();
        });

        // Sentences.
        Schema::create('sentences', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->text('contents');
            $table->timestamps();
            $table->softDeletes();
        });

        // Media.
        Schema::create('media', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('definition_id')->unsigned();
            $table->foreign('definition_id')
                ->references('id')
                ->on('definitions')
                ->onDelete('cascade');
            $table->string('type');         // MIME types.
            $table->text('location');       // ??
            $table->timestamps();
            $table->softDeletes();
        });

        // Data.
        Schema::create('data', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('parent_id')->unsigned();
            $table->string('parent_type');
            $table->text('contents');
        });

        // Cultures.
        Schema::create('cultures', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->foreign('language_id')
                ->references('id')
                ->on('languages');
            $table->string('name', 400);
            $table->string('alt_names', 400)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Countries.
        Schema::create('countries', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('name', 400);
            $table->string('alt_names', 400)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Users.
        Schema::create('users', function(Blueprint $table)
		{
            $table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 70);
			$table->string('email', 100)->unique();
			$table->string('password', 60);
			$table->rememberToken();
            $table->text('params');
			$table->timestamps();
            $table->softDeletes();
		});

        // Roles: handled by zizaco/entrust.

        // Permissions: handled by zizaco/entrust.

        // Pivot tables.
        Schema::create('definition_language', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';
            $table->integer('language_id')->unsigned();
            $table->integer('definition_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop everything except for password_resets and recreate the previous structure.
        Schema::hasTable('users') ? Schema::drop('users') : null;
        Schema::hasTable('languages') ? Schema::drop('languages') : null;
        Schema::hasTable('definitions') ? Schema::drop('definitions') : null;
        Schema::hasTable('translations') ? Schema::drop('translations') : null;
        Schema::hasTable('definition_language') ? Schema::drop('definition_language') : null;

        Schema::create('users', function(Blueprint $table)
		{
            $table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 70);
			$table->string('email', 100)->unique();
			$table->string('password', 60);
			$table->rememberToken();
			$table->timestamps();
		});

		Schema::create('languages', function(Blueprint $table)
		{
            $table->engine = 'InnoDB';

			$table->increments('id')->length(6);
            $table->string('code', 7)->unique();
            $table->string('parent_code', 7)->nullable()->index();
            $table->string('name', 100);
            $table->string('alt_names', 300)->nullable();
            $table->string('countries', 60)->nullable();
            $table->text('params');
			$table->timestamps();
            $table->softDeletes();
		});

		Schema::create('definitions', function(Blueprint $table)
		{
            $table->engine = 'InnoDB';

			$table->increments('id')->length(9);
            $table->string('title', 400);
            $table->string('alt_titles', 400)->nullable();
            $table->text('data')->nullable();
            $table->tinyInteger('type')->unsigned();
            $table->string('sub_type');
            $table->text('tags')->nullable();
            $table->tinyInteger('state');
            $table->text('params');
			$table->timestamps();
            $table->softDeletes();
		});

        Schema::create('translations', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('definition_id')->length(9)->unsigned();
            $table->foreign('definition_id')
                ->references('id')
                ->on('definitions')
                ->onDelete('cascade');
            $table->string('language', 3)->index();
            $table->text('translation');
            $table->text('literal');
            $table->text('meaning');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('definition_language', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->integer('language_id')->length(6)->unsigned();
            $table->integer('definition_id')->length(9)->unsigned();
        });

        // Re-create the fulltext indices.
        DB::statement('CREATE FULLTEXT INDEX idx_name ON languages (name, alt_names)');
		DB::statement('CREATE FULLTEXT INDEX idx_title ON definitions (title, alt_titles)');
		DB::statement('CREATE FULLTEXT INDEX idx_data ON definitions (data)');
		DB::statement('CREATE FULLTEXT INDEX idx_tags ON definitions (tags)');
		DB::statement('CREATE FULLTEXT INDEX idx_translation ON translations (translation, literal, meaning)');
    }
}
