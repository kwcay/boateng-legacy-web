<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
namespace App\Factories\DataImport;

use Exception;
use App\Models\Alphabet;
use App\Models\Language;
use App\Factories\DataImportFactory;

class LanguageImportFactory extends DataImportFactory
{
    /**
     * Stores loaded alphabets.
     */
    private $_alphabets = [];

    /**
     *
     */
    public function importDataSet()
    {
        // Loop through languages and import them one by one.
        $saved = $skipped = 0;
        foreach ($this->dataArray as $langArray)
        {
            // Performance check.
            if (!array_key_exists('code', $langArray) || Language::findByCode($langArray['code'])) {
                $skipped++;
                continue;
            }

            // Create a language object.
            $lang = Language::create(array_only($langArray, [
                'code',
                'parentCode',
                'name',
                'transliteration',
                'altNames',
                'createdAt',
                'deletedAt',
            ]));

            // Add alphabets.
            if (array_key_exists('alphabets', $langArray) && is_array($langArray['alphabets']))
            {
                // TODO
                // ...
            }

            // Or try to find alphabets for this language.
            elseif ($alphabets = $this->findAlphabets($lang->code))
            {
                $lang->alphabets()->attach($alphabets);
            }

            // TODO: relations to be added:
            // Has Many: Culture
            // Has Many: Data
            // Has Many: Country
            // Has Many: Script

            $saved++;
        }

        $this->setMessage($saved .' of '. ($saved + $skipped) .' languages updated.');

        return $this;
    }

    /**
     *
     */
    private function getAlphabet($code)
    {
        // If an alphabet was already retrieved from the database, pull it from the local array.
        if (array_key_exists($this->_alphabets, $code)) {
            return $this->_alphabets[$code];
        }

        // Else, try to fetch it from the database.
        if ($alphabet = Alphabet::where('code', '=', $code)->first()) {
            $this->_alphabets[$code] = $alphabet->id;
            return $alphabet->id;
        }

        return null;
    }

    /**
     * @param string $langCode
     * @return array
     */
    private function findAlphabets($langCode)
    {
        $found = [];

        // Loop through loaded alphabets first.
        foreach ($this->_alphabets as $alphabetCode => $alphabetId)
        {
            if (strpos($alphabetCode, $langCode .'-') === 0) {
                $found[] = $alphabetId;
            }
        }

        // Lookup alphabets in the database as well.
        $more = Alphabet::where('code', 'LIKE', $langCode .'-%')->whereNotIn('id', $found)->lists('id', 'code');
        if (count($more)) {
            $found = array_merge($found, $more->toArray());
            $this->_alphabets = array_merge($this->_alphabets, $more->toArray());
        }

        return $found;
    }
}
