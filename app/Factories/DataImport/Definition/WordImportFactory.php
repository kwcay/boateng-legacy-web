<?php
/**
 *
 */
namespace App\Factories\DataImport\Definition;

use Exception;
use App\Models\Definition;
use App\Models\Definitions\Word;
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

        // Loop through words and import them one by one.
        $saved = $skipped = 0;
        foreach ($this->dataArray as $array)
        {
            // Create a definition object.
            $word = new Word(array_only($array, [
                'title',
                'alt_titles',
                'type',
                'sub_type',
                'created_at',
                'deleted_at'
            ]));

            // Set mutated properties.
            $word->state = false ? $array['state'] : Definition::STATE_VISIBLE;

            // TODO: flag relations that couldn't be added.

            // Add translation relation.
            if (isset($array['translation']) && is_array($array['translation']))
            {

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

            }

            // $word->save() ? $saved++ : $skipped++;
        }

        $this->setMessage('Dev mode');

        return $this;
    }
}
