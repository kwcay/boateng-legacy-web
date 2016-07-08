<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
namespace App\Http\Controllers;

use Session;
use Redirect;
use Request;
use Validator;
use App\Http\Requests;
use App\Models\Country;
use App\Models\Alphabet;
use App\Models\Language;
use App\Models\Definition;
use App\Models\Definitions\Word;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;

/**
 * @abstract Main controller for the Language resource.
 */
class LanguageController extends Controller
{
    /**
     *
     */
    protected $defaultQueryLimit = 20;

    /**
     *
     */
    protected $supportedOrderColumns = [
        'id' => 'ID',
        'code' => 'ISO 639-3 code',
        'name' => 'Name',
        'createdAt' => 'Created date',
    ];

    /**
     *
     */
    protected $defaultOrderColumn = 'code';

    /**
     *
     */
    protected $defaultOrderDirection = 'asc';

    /**
     * Display the language page.
     *
     * @param string $id    Either the ISO 639-3 language code or language ID.
     * @return Response
     */
    public function show($id)
    {
        // Retrieve the language object.
        if (!$lang = $this->getLanguage($id)) {
            abort(404, 'Can\'t find that languge :(');
        }

        // TODO: count number of words, not all definitions.
        $total = $lang->definitions()->count();
        $first = $latest = $random = null;

        // Retrieve first definition.
        if ($total > 0) {
            $first = $lang->definitions()->with('titles')->orderBy('created_at', 'asc')->first();
        }

        // Retrieve latest definition.
        if ($total > 1) {
            $latest = $lang->definitions()->with('titles')->orderBy('created_at', 'desc')->first();
        }

        // Retrieve random definition.
        if ($total > 2) {
            $random = Word::random($lang);
        }

        return view('pages.language', [
            'lang' => $lang,
            'random' => $random,
            'first' => $first,
            'latest' => $latest
        ]);
    }

	/**
	 * Show the form for editing a language.
	 *
     * @param   string  $id     Either the ISO 639-3 language code or language ID.
	 * @return  Response
	 */
	public function edit($id)
    {
        // Retrieve the language object.
        if (!$lang = $this->getLanguage($id)) {
            abort(404, 'Can\'t find that languge :(');
        }

        // Alphabet data for selectize plugin.
        $alphabetOptions = $lang->alphabets->map(function($item) {
            return [
                'code' => $item->code,
                'name' => $item->name,
                'transliteration' => $item->transliteration,
            ];
        });

        // Parent language data for selectize plugin.
        $parentOptions = [];
        if ($lang->parent)
        {
            $parentOptions[] = [
                'code' => $lang->parent->code,
                'name' => $lang->parent->name,
                'transliteration' => $lang->parent->transliteration,
                'altNames' => $lang->parent->altNames,
            ];
        }

        // Country data for selectize plugin.
        $countryOptions = $lang->countries->map(function($item) {
            return [
                'code' => $item->code,
                'name' => $item->name,
                'altNames' => $item->altNames,
            ];
        });

        return view('admin.language.edit', [
            'model' => $lang,
            'alphabetOptions' => $alphabetOptions,
            'parentOptions' => $parentOptions,
            'countryOptions' => $countryOptions,
        ]);
    }

	/**
	 * Update the specified resource in storage.
     *
	 * @param int $id
	 * @return Response
	 */
	public function update($id)
	{
        // Retrieve the language object.
        if (!$lang = Language::find($id)) {
            abort(404);
        }

        $this->validate($this->request, (new Language)->validationRules);

        // Update attributes.
        $lang->fill($this->request->only([
            'parentCode',
            'name',
            'transliteration',
            'altNames'
        ]));

        if (!$lang->save()) {
            abort(500);
        }

        // Update alphabets.
        $alphabets = $this->getAlphabets($this->request->get('alphabets', ''));
        $alphabetIDs = $alphabets->map(function($item) {
            return $item->id;
        })->toArray();

        $lang->alphabets()->sync($alphabetIDs);

        // Update countries.
        $countries = $this->getCountries($this->request->get('countries', ''));
        $countryIDs = $countries->map(function($item) {
            return $item->id;
        })->toArray();

        $lang->countries()->sync($countryIDs);

        // Send success message to client, and a thank you.
        Session::push('messages', 'The details for <em>'. $lang->name .
            '</em> were successfully saved, thanks :)');

        // Return URI
        switch ($this->request->get('return'))
        {
            case 'admin':
                $return = route('admin.language.index');
                break;

            case 'edit':
                $return = $lang->editUri;
                break;

            case 'finish':
            case 'summary':
            default:
                $return = $lang->uri;
        }

        return redirect($return);
	}

    /**
     * Shortcut to retrieve a language object.
     *
     * @param string $id    Either the ISO 639-3 language code or language ID.
     * @return \App\Models\Language|null
     */
    protected function getLanguage($id, array $embed = ['parent'])
    {
        // Performance check.
        if (empty($id) || is_numeric($id) || !is_string($id)) {
            return null;
        }

        // Find langauge by code.
        if (strlen($id) == 3 || strlen($id) == 7) {
            $lang = Language::findByCode($id, $embed);
        }

        // Find language by ID.
        elseif ($id = Language::decodeId($id)) {
            $lang = Language::with($embed)->find($id);
        }

        else {
            $lang = null;
        }

        return $lang;
    }

    /**
     * Retrieves alphabet models.
     *
     * @param string $raw   Alphabet codes, separated by commas.
     * @return Illuminate\Support\Collection
     */
    protected function getAlphabets($raw = null)
    {
        $alphabets = new Collection;

        // Retrieve alphabet models.
        $raw = trim($raw);
        if (strlen($raw))
        {
            foreach (@explode(',', $raw) as $code)
            {
                if ($alphabet = Alphabet::findByCode($code)) {
                    $alphabets->push($alphabet);
                }
            }
        }

        return $alphabets;
    }

    /**
     * Retrieves country models.
     *
     * @param string $raw   Country codes, separated by commas.
     * @return Illuminate\Support\Collection
     */
    protected function getCountries($raw = null)
    {
        $countries = new Collection;

        // Retrieve alphabet models.
        $raw = trim($raw);
        if (strlen($raw))
        {
            foreach (@explode(',', $raw) as $code)
            {
                if ($country = Country::findByCode($code)) {
                    $countries->push($country);
                }
            }
        }

        return $countries;
    }
}
