<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
namespace App\Factories\DataImport;

use Exception;

use App\Models\Alphabet;
use App\Models\Language;
use App\Models\Definition;
use App\Models\Translation;
use App\Models\DefinitionTitle;
use App\Factories\DataImportFactory;

class DefinitionImportFactory extends DataImportFactory
{
    /**
     *
     */
    public function importDataSet()
    {
        // Stores loaded languages.
        $loadedLanguages = [];

        // Loop through definitions and import them one by one.
        $saved = $skipped = 0;
        foreach ($this->dataArray as $data)
        {
            // Determine definition titles.
            $titles = [];

            if (array_key_exists('titles', $data) && is_array($data['titles']))
            {
                foreach ($data['titles'] as $titleData) {
                    $titles[] = new DefinitionTitle($titleData);
                }
            }

            elseif (array_key_exists('title', $data) && is_string($data['title']))
            {
                $titles[] = new DefinitionTitle([
                    'title' => trim($data['title'])
                ]);

                // Create a title record for each altTitle from the old format.
                if (array_key_exists('altTitles', $data) && is_string($data['altTitles']))
                {
                    $altTitles = @explode(',', $data['altTitles']);

                    foreach ($altTitles as $alt)
                    {
                        $alt = trim($alt);

                        if (strlen($alt)) {
                            $titles[] = new DefinitionTitle(['title' => $alt]);
                        }
                    }
                }
            }

            // Performance check.
            if (!count($titles)) {
                $skipped++;
                continue;
            }

            // Retrieve translations.
            $translations = [];
            if (array_key_exists('translation', $data) && is_array($data['translation']))
            {
                // Retrieve translations.
                $practical  = isset($data['translation']['practical']) ? $data['translation']['practical'] : [];
                $literal    = isset($data['translation']['literal']) ? $data['translation']['literal'] : [];
                $meaning    = isset($data['translation']['meaning']) ? $data['translation']['meaning'] : [];

                foreach ($practical as $lang => $practicalTranslation)
                {
                    // Make sure we have a 3-letter code.
                    $langCode = $lang == 'en' ? 'eng' : $lang;

                    $translations[] = new Translation([
                        'language' => $langCode,
                        'practical' => $practical[$lang],
                        'literal' => $literal[$lang],
                        'meaning' => $meaning[$lang]
                    ]);
                }
            }

            // Performance check.
            if (!count($translations)) {
                $skipped++;
                continue;
            }

            // Retrieve languages.
            $languages = [];
            if (array_key_exists('language', $data) && is_array($data['language']))
            {
                foreach ($data['language'] as $code => $name)
                {
                    // Check if language has already been loaded.
                    if (isset($loadedLanguages[$code])) {
                        $languages[] = $loadedLanguages[$code];
                    }

                    // If not, attempt to retrieve it from the database.
                    elseif ($lang = Language::findByCode($code)) {
                        $languages[] = $lang;
                        $loadedLanguages[$code] = $lang;
                    }

                    // If the language is not in our database, try to create a record for it.
                    elseif ($lang = Language::create(['code' => $code, 'name' => $name])) {
                        $languages[] = $lang;
                        $loadedLanguages[] = $lang;
                    }

                    else {
                        $this->setMessage('Could not add related language "'. $code .'".');
                    }
                }
            }

            // Performance check.
            if (!count($languages)) {
                $skipped++;
                continue;
            }

            // Retrieve definition attributes.
            $attributes = [
                'type' => Definition::getTypeConstant($data['type']),
                'sub_type' => Definition::getSubTypeAbbreviation(Definition::getTypeConstant($data['type']), $data['subType']),
                'main_language_code' => array_get($data, 'mainLanguageCode', null),
                'rating' => array_get($data, 'rating', 1),
                'meta' => array_get($data, 'meta', null),
                'created_at' => array_get($data, 'createdAt', null),
                'deleted_at' => array_get($data, 'deletedAt', null),
            ];

            // Create a definition object and save it right away, so that we can add the
            // relations afterwards.
            $definition = Definition::create($attributes);

            // Add definition titles.
            $definition->titles()->saveMany($titles);

            // Add translations.
            $definition->translations()->saveMany($translations);

            // Add data relation.
            if (array_key_exists('data', $data) && is_array($data['data']))
            {

            }

            // Add media relation.
            if (array_key_exists('media', $data) && is_array($data['media']))
            {

            }

            // Add tag relations.
            if (array_key_exists('tags', $data))
            {
                $tags = @explode(',', $data['tags']);
            }

            // Add languages.
            $definition->languages()->saveMany($languages);

            // Save the main languge.
            if (empty($definition->mainLanguageCode)) {
                $definition->mainLanguageCode = $languages[0]->code;
                $definition->save();
            }

            $saved++;
        }

        $this->setMessage($saved .' of '. ($saved + $skipped) .' definitions added to database.');

        return $this;
    }
}
