<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Architecture041 extends Migration
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
            $table->string('name', 100);
            $table->string('alt_names', 300)->nullable();
			$table->timestamps();
            $table->softDeletes();
		});
        DB::statement('CREATE FULLTEXT INDEX idx_name ON languages (name, alt_names)');

        // Language scripts.
        Schema::create('scripts', function(Blueprint $table)
		{
            $table->engine = 'InnoDB';

			$table->increments('id');
            $table->string('name', 100)->unique();
            $table->string('abbreviation', 10)->unique();
            $table->text('alphabet')->nullable();
            $table->text('partial_alphabet')->nullable();
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
            $table->text('params');
			$table->timestamps();
            $table->softDeletes();
		});
        DB::statement('CREATE FULLTEXT INDEX idx_title ON definitions (title, alt_titles)');

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
        DB::statement('CREATE FULLTEXT INDEX idx_translation ON translations (practical, literal, meaning)');

        // Tags.
        Schema::create('tags', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('title', 100)->unique();
            $table->timestamps();
        });
        DB::statement('CREATE FULLTEXT INDEX idx_title ON tags (title)');

        // Sentences.
        Schema::create('sentences', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->text('content');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('CREATE FULLTEXT INDEX idx_content ON sentences (content)');

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
            $table->text('content');
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
            $table->string('alt_names', 400)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('CREATE FULLTEXT INDEX idx_name ON cultures (name, alt_names)');

        // Countries.
        Schema::create('countries', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('name', 400);
            $table->string('alt_names', 400)->nullable();
            $table->string('code', 3)->unique();
            $table->timestamps();
            $table->softDeletes();
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
			$table->rememberToken();
            $table->text('params')->nullable();
			$table->timestamps();
            $table->softDeletes();
		});

        // Roles: handled by zizaco/entrust.
        Schema::create('roles', function(Blueprint $table)
		{
            $table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 100)->unique();
			$table->string('display_name')->nullable();
            $table->text('description')->nullable();
		});

        // Permissions: handled by zizaco/entrust.
        Schema::create('permissions', function (Blueprint $table)
        {
            $table->increments('id');
            $table->string('name', 100)->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Pivot tables.
        $pivots = [
            'country_culture',
            'country_language',
            'definition_language',
            'definition_sentence',
            'definition_tag',
            'language_script',
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

        // Role-User (Many-to-Many).
        Schema::create('role_user', function (Blueprint $table)
        {
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->primary(['user_id', 'role_id']);
        });

        // Permission-Role (Many-to-Many)
        Schema::create('permission_role', function (Blueprint $table)
        {
            $table->integer('permission_id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
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
        Schema::hasTable('languages') ? DB::statement('ALTER TABLE languages DROP INDEX idx_name') : null;
        Schema::hasTable('definitions') ? DB::statement('ALTER TABLE definitions DROP INDEX idx_title') : null;
        Schema::hasTable('translations') ? DB::statement('ALTER TABLE translations DROP INDEX idx_translation') : null;
        Schema::hasTable('tags') ? DB::statement('ALTER TABLE tags DROP INDEX idx_title') : null;
        Schema::hasTable('sentences') ? DB::statement('ALTER TABLE sentences DROP INDEX idx_content') : null;
        Schema::hasTable('data') ? DB::statement('ALTER TABLE data DROP INDEX idx_content') : null;
        Schema::hasTable('cultures') ? DB::statement('ALTER TABLE cultures DROP INDEX idx_name') : null;
        Schema::hasTable('countries') ? DB::statement('ALTER TABLE countries DROP INDEX idx_name') : null;
        $drop = [
            'country_culture', 'country_language', 'definition_language', 'definition_sentence',
            'definition_tag', 'language_script', 'permission_role', 'role_user',
            'scripts', 'tags', 'sentences', 'media', 'cultures', 'countries', 'data',
            'roles', 'permissions', 'users', 'translations', 'definitions', 'languages',
        ];

        foreach ($drop as $table) {
            if (Schema::hasTable($table)) {
                Schema::drop($table);
            }
        }

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
