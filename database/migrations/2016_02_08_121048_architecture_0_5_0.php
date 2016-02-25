<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Architecture050 extends Migration
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
        Schema::hasTable('definition_language') ? Schema::drop('definition_language') : null;
        if (Schema::hasTable('translations'))
        {
            DB::statement('ALTER TABLE translations DROP INDEX idx_translation');
            Schema::drop('translations');
        }
        if (Schema::hasTable('languages'))
        {
            DB::statement('ALTER TABLE languages DROP INDEX idx_name');
            Schema::drop('languages');
        }
        if (Schema::hasTable('definitions'))
        {
            DB::statement('ALTER TABLE definitions DROP INDEX idx_title');
            DB::statement('ALTER TABLE definitions DROP INDEX idx_data');
            DB::statement('ALTER TABLE definitions DROP INDEX idx_tags');
            Schema::drop('definitions');
        }

        // Languages.
        Schema::create('languages', function(Blueprint $table)
		{
            $table->engine = 'InnoDB';

			$table->increments('id');

            $table->string('code', 7)->unique();
            $table->string('parent_code', 7)->nullable()->index();
            $table->string('name', 200);
            $table->string('transliteration', 200);
            $table->string('alt_names', 300)->nullable();

			$table->timestamps();
            $table->softDeletes();
		});
        DB::statement('CREATE FULLTEXT INDEX idx_transliteration ON languages (transliteration)');

        // Alpabets.
        Schema::create('alphabets', function(Blueprint $table)
		{
            $table->engine = 'InnoDB';

			$table->increments('id');

            $table->string('name', 100)->unique();
            $table->string('transliteration', 100)->unique();
            $table->string('code', 10)->unique();
            $table->string('script_code', 4)->nullable();
            $table->text('letters')->nullable();

			$table->timestamps();
            $table->softDeletes();
		});
        DB::statement('CREATE FULLTEXT INDEX idx_transliteration ON alphabets (transliteration)');

        // Definitions.
        Schema::create('definitions', function(Blueprint $table)
		{
            $table->engine = 'InnoDB';

			$table->increments('id');

            $table->tinyInteger('type')->unsigned();
            $table->string('sub_type', 10);
            $table->string('main_language_code', 7);
            $table->tinyInteger('rating');
            $table->text('meta');

			$table->timestamps();
            $table->softDeletes();
		});

        // Definition titles.
        Schema::create('definition_titles', function(Blueprint $table)
		{
            $table->engine = 'InnoDB';

			$table->increments('id');

            $table->integer('definition_id')->unsigned();
            $table->foreign('definition_id')
                ->references('id')
                ->on('definitions')
                ->onDelete('cascade');

            $table->integer('alphabet_id')->unsigned()->nullable();
            $table->foreign('alphabet_id')
                ->references('id')
                ->on('alphabets')
                ->onDelete('cascade');

            $table->string('title', 400);
            $table->string('transliteration', 400);
            $table->string('reference');

			$table->timestamps();
            $table->softDeletes();
		});
        DB::statement('CREATE FULLTEXT INDEX idx_transliteration ON definition_titles (transliteration)');

        // Media.
        Schema::create('media', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');

            $table->integer('parent_id')->unsigned();
            $table->string('parent_type');
            $table->string('mime_type');
            $table->string('disk', 40);
            $table->string('path');

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
            $table->text('literal')->nullable();    // Literal translation.
            $table->text('meaning')->nullable();    // Elaboration on the meaning of the definition.

            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('CREATE FULLTEXT INDEX idx_practical ON translations (practical)');
        DB::statement('CREATE FULLTEXT INDEX idx_literal ON translations (literal)');
        DB::statement('CREATE FULLTEXT INDEX idx_meaning ON translations (meaning)');

        // Tags.
        Schema::create('tags', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');

            $table->string('title', 150)->unique();
        });
        DB::statement('CREATE FULLTEXT INDEX idx_title ON tags (title)');

        // Descriptions and other textual data.
        Schema::create('data', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');

            $table->integer('parent_id')->unsigned();

            $table->string('parent_type');
            $table->text('content');

            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('CREATE FULLTEXT INDEX idx_content ON data (content)');

        // Cultures.
        Schema::create('cultures', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');

            $table->integer('language_id')->unsigned();
            $table->foreign('language_id')
                ->references('id')
                ->on('languages');

            $table->string('name', 400);
            $table->string('transliteration', 400);
            $table->string('alt_names', 400);

            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('CREATE FULLTEXT INDEX idx_transliteration ON cultures (transliteration)');

        // Countries.
        Schema::create('countries', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');

            $table->string('name', 400);
            $table->string('alt_names', 400)->nullable();
            $table->string('code', 2)->unique();

            $table->timestamps();
        });
        DB::statement('CREATE FULLTEXT INDEX idx_name ON countries (name, alt_names)');

        // Users.
        Schema::create('users', function(Blueprint $table)
		{
            $table->engine = 'InnoDB';

			$table->increments('id');

			$table->string('name', 70);
			$table->string('email', 100)->unique();
			$table->string('password', 60);
            $table->text('params')->nullable();
			$table->rememberToken();

			$table->timestamps();
		});

        // Pivot tables.
        $pivots = [
            'country_culture',
            'country_language',
            'definition_language',
            'definition_tag',
            'alphabet_language',
        ];

        foreach ($pivots as $pivot)
        {
            Schema::create($pivot, function(Blueprint $table) use($pivot)
            {
                list($table1, $table2) = explode('_', $pivot);

                $table->engine = 'InnoDB';
                $table->integer($table1 .'_id')->unsigned();
                $table->integer($table2 .'_id')->unsigned();
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
        // Drop everything except for password_resets. The migration for 0.4.1 will recreate
        // the previous sctructure.
        Schema::hasTable('languages') ? DB::statement('ALTER TABLE languages DROP INDEX idx_transliteration') : null;
        Schema::hasTable('alphabets') ? DB::statement('ALTER TABLE alphabets DROP INDEX idx_transliteration') : null;
        Schema::hasTable('definition_titles') ? DB::statement('ALTER TABLE definition_titles DROP INDEX idx_transliteration') : null;
        Schema::hasTable('translations') ? DB::statement('ALTER TABLE translations DROP INDEX idx_practical') : null;
        Schema::hasTable('translations') ? DB::statement('ALTER TABLE translations DROP INDEX idx_literal') : null;
        Schema::hasTable('translations') ? DB::statement('ALTER TABLE translations DROP INDEX idx_meaning') : null;
        Schema::hasTable('tags') ? DB::statement('ALTER TABLE tags DROP INDEX idx_title') : null;
        Schema::hasTable('data') ? DB::statement('ALTER TABLE data DROP INDEX idx_content') : null;
        Schema::hasTable('cultures') ? DB::statement('ALTER TABLE cultures DROP INDEX idx_transliteration') : null;
        Schema::hasTable('countries') ? DB::statement('ALTER TABLE countries DROP INDEX idx_name') : null;
        $drop = [
            'country_culture', 'country_language', 'definition_language', 'definition_sentence',
            'definition_tag', 'alphabet_language', 'permission_role', 'role_user',
            'users', 'countries', 'cultures', 'data', 'tags', 'translations', 'media',
            'definition_titles', 'definitions', 'alphabets', 'languages',
        ];

        foreach ($drop as $table) {
            if (Schema::hasTable($table)) {
                Schema::drop($table);
            }
        }
    }
}
