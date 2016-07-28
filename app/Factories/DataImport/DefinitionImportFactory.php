<?php
/**
 * Copyright Di Nkɔmɔ(TM) 2015, all rights reserved.
 */
namespace App\Factories\DataImport;

use App\Models\Tag;
use App\Models\Language;
use App\Models\Definition;
use App\Models\Translation;
use App\Models\DefinitionTitle;
use App\Factories\DataImportFactory;

class DefinitionImportFactory extends DataImportFactory
{
    /**
     * Stores loaded languages.
     *
     * @var array
     */
    private $_languages = [];

    /**
     * Stores loaded tags.
     *
     * @var array
     */
    private $_tags = [];


    public function importDataSet()
    {
        // Loop through definitions and import them one by one.
        $saved = $skipped = 0;
        foreach ($this->dataArray as $data) {
            // TODO: check database for duplicates somehow...
            // ...

            // Definition titles
            $titles = $titleData = [];

            if (array_key_exists('titles', $data) && is_array($data['titles'])) {
                $titleData = $data['titles'];
            } elseif (array_key_exists('titlesArray', $data) && is_array($data['titlesArray'])) {
                $titleData = $data['titlesArray'];
            } elseif (array_key_exists('titleList', $data) && is_array($data['titleList'])) {
                $titleData = $data['titleList'];
            }

            if (count($titleData)) {
                foreach ($titleData as $title) {
                    $titles[] = new DefinitionTitle($title);
                }
            }

            // Performance check.
            if (! count($titles)) {
                $skipped++;
                continue;
            }

            // Retrieve translations.
            $translations = [];
            if (array_key_exists('translationData', $data) && is_array($data['translationData'])) {
                foreach ($data['translationData'] as $langCode => $translation) {
                    $translations[] = new Translation([
                        'language'  => $langCode,
                        'practical' => $translation['practical'],
                        'literal'   => $translation['literal'],
                        'meaning'   => $translation['meaning'],
                    ]);
                }
            }

            // Performance check.
            if (! count($translations)) {
                $skipped++;
                continue;
            }

            // Retrieve languages.
            $languages = $languageData = [];
            if (array_key_exists('language', $data) && is_array($data['language'])) {
                $languageData = $data['language'];
            } elseif (array_key_exists('languageList', $data) && is_array($data['languageList'])) {
                $languageData = $data['languageList'];
            }

            foreach ($languageData as $code => $name) {
                // Check if language has already been loaded.
                if (isset($this->_languages[$code])) {
                    $languages[] = $this->_languages[$code];
                }

                // If not, attempt to retrieve it from the database.
                elseif ($lang = Language::findByCode($code)) {
                    $languages[] = $lang;
                    $this->_languages[$code] = $lang;
                }

                // If the language is not in our database, try to create a record for it.
                elseif ($lang = Language::create(['code' => $code, 'name' => $name])) {
                    $languages[] = $lang;
                    $this->_languages[] = $lang;
                } else {
                    $this->setMessage('Could not add related language "'.$code.'".');
                }
            }

            // Performance check.
            if (! count($languages)) {
                $skipped++;
                continue;
            }

            // Retrieve definition attributes.
            $attributes = [
                'type' => Definition::getTypeConstant($data['type']),
                'sub_type' => Definition::getSubTypeAbbreviation(Definition::getTypeConstant($data['type']), $data['subType']),
                'main_language_code' => array_get($data, 'mainLanguageCode', $languages[0]->code),
                'rating' => array_get($data, 'rating', 1),
                'meta' => array_get($data, 'meta', ''),
                'created_at' => array_get($data, 'createdAt', null),
                'deleted_at' => array_get($data, 'deletedAt', null),
            ];

            if (! strlen($attributes['meta'])) {
                $attributes['meta'] = '{}';
            }

            // Create a definition object and save it right away, so that we can add the
            // relations afterwards.
            $definition = Definition::firstOrCreate($attributes);

            // Add definition titles.
            $definition->titles()->saveMany($titles);

            // Add translations.
            $definition->translations()->saveMany($translations);

            // Add data relation.
            if (array_key_exists('data', $data) && is_array($data['data'])) {
                // TODO ...
            }

            // Add media relation.
            if (array_key_exists('media', $data) && is_array($data['media'])) {
                // TODO ...
            }

            // Add tag relations.
            if (array_key_exists('tagList', $data) && is_array($data['tagList'])) {
                $tags = [];

                foreach ($data['tagList'] as $tag) {
                    $tags[] = Tag::firstOrCreate(['title' => $tag])->id;
                }

                if (count($tags)) {
                    $definition->tags()->sync($tags);
                }
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

        $this->setMessage($saved.' of '.($saved + $skipped).' definitions updated.');

        return $this;
    }
}
