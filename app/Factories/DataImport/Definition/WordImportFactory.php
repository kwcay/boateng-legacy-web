<?php
/**
 *
 */
namespace App\Factories\DataImport\Definition;

use Exception;
use App\Models\Language;
use App\Models\Definition;
use App\Models\Definitions\Word;
use App\Models\Translation;
use App\Factories\DataImportFactory;

class WordImportFactory extends DataImportFactory
{
    /**
     *
     */
    public function importDataSet()
    {
        // TODO: check database for duplicates
        // ...

        // TODO: check meta for languages to pre-load for relations.
        // In the mean time, we use a this array to save the languages already loaded.
        $loadedLanguages = [];

        // Loop through words and import them one by one.
        $saved = $skipped = 0;
        foreach ($this->dataArray as $array)
        {
            // Create a definition object and save it right away, so that we can add the
            // relations afterwards.
            $word = Word::create(array_only($array, [
                'title',
                'alt_titles',
                'type',
                'sub_type',
                'state',
                // 'params',
                'created_at',
                'deleted_at'
            ]));

            // TODO: flag relations that couldn't be added.

            // Add translation relation.
            if (isset($array['translation']) && is_array($array['translation']))
            {
                // Retrieve translations.
                $practical = isset($array['translation']['practical']) ? $array['translation']['practical'] : [];
                $literal = isset($array['translation']['literal']) ? $array['translation']['literal'] : [];
                $meaning = isset($array['translation']['meaning']) ? $array['translation']['meaning'] : [];

                // Save translations.
                foreach ($practical as $lang => $data)
                {
                    // Make sure we have a 3-letter code.
                    $langCode = $lang == 'en' ? 'eng' : $lang;

                    $word->translations()->create([
                        'language' => $langCode,
                        'practical' => $practical[$lang],
                        'literal' => $literal[$lang],
                        'meaning' => $meaning[$lang],
                        'created_at' => $word->createdAt
                    ]);
                }
            }

            // Add data relation.
            if (isset($array['data']))
            {

            }

            // Add media relation.
            if (isset($array['media']) && is_array($array['media']))
            {

            }

            // Add sentence relations.
            // ...

            // Add tag relations.
            if (isset($array['tags']))
            {
                $tags = @explode(',', $array['tags']);
            }

            // Add language relation.
            if (isset($array['language']) && is_array($array['language']))
            {
                $languages = [];

                foreach ($array['language'] as $code => $name)
                {
                    if (isset($loadedLanguages[$code])) {
                        $languages[] = $loadedLanguages[$code];
                    }

                    elseif ($lang = Language::findByCode($code)) {
                        $languages[] = $lang;
                        $loadedLanguages[] = $lang;
                    }

                    else {
                        $this->setMessage('Could not add related language "'. $code .'" to "'. $word->title .'"');
                    }
                }

                $word->languages()->saveMany($languages);
            }

            $saved++;
        }

        $this->setMessage($saved .' of '. ($saved + $skipped) .' words added to database.');

        return $this;
    }
}
