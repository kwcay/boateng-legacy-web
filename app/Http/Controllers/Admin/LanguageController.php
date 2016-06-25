<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 * @todo    Deprecate for App\Http\Controllers\LanguageController ?
 */
namespace App\Http\Controllers\Admin;

use Lang;
use Session;
use Redirect;
use Request;
use Validator;
use App\Http\Requests;
use App\Models\Alphabet;
use App\Models\Country;
use App\Models\Language;
use App\Models\Definition;
use App\Models\Definitions\Word;
use Illuminate\Support\Collection;
use App\Http\Controllers\Admin\BaseController as Controller;

class LanguageController extends Controller
{
    /**
     *
     */
    protected $name = 'language';

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
	 * Store a newly created resource in storage.
     *
     * TODO: integrate with API.
	 *
	 * @return Response
	 */
	public function store()
	{
        // Retrieve the language details.
        $data = Request::only(['code', 'parent_code', 'name', 'alt_names']);

        // Set return route.
        $return = Request::input('next') == 'continue' ? 'edit' : 'index';

        return $this->save(new Language, $data, $return);
	}

	/**
	 * Show the form for editing a language.
	 *
     * @param string $code  Language code.
	 * @return Response
	 */
	public function edit($code)
    {
        // Retrieve the language model.
        if (!$lang = Language::findByCode($code, ['parent'])) {
            abort(404);
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
	 * Removes the specified resource from storage.
	 *
	 * @param int $id
	 * @return Response
	 */
	public function destroy($id)
	{
        // Retrieve the language model.
        if (!$lang = Language::find($id)) {
            throw new \Exception(trans('errors.resource_not_found'), 404);
        }

        // Delete record
        Session::push('messages', '<em>'. $lang->name .'</em> has been succesfully deleted.');
        $lang->delete();

        // Return URI
        switch ($this->request->get('return'))
        {
            case 'home':
                $return = route('home');
                break;

            case 'admin':
            default:
                $return = route('admin.language.index');
                break;
        }

        return redirect($return);
	}

    /**
     * Shortcut to create a new language or save an existing one.
     *
     * TODO: integrate with API.
     *
     * @param \App\Models\Language $lang    Language object.
     * @param array $data                   Language details to update.
     * @param string $return                Relative URI to redirect to.
     * @return Response
     */
    public function save($lang, $data, $return)
    {
        // Validate input data
        $test = Language::validate($data);
        if ($test->fails())
        {
            // Flash input data to session
            Request::flashExcept('_token');

            // Return to form
            $return = $lang->exists ? route('language.edit', ['id' => $lang->getId()]) : route('language.create');
            return redirect($return)->withErrors($test);
        }

        // Parent language details
        // if (strlen($data['parent_code']) >= 3 && $parent = Language::findByCode($data['parent_code'])) {
        //     $lang->setParam('parentName', $parent->name);
        // } else {
        //     $data['parent_code'] = '';
        //     $lang->setParam('parentName', '');
        // }

        // Update language details.
        $lang->fill($data);
        $lang->save();

        // ...
        switch ($return)
        {
            case 'index':
                $return = $lang->uri;
                break;

            case 'edit':
                $return = route('language.edit', ['code' => $lang->code]);
                break;

            case 'add':
                $return = route('language.create');
                break;
        }

        Session::push('messages', 'The details for <em>'. $lang->name .'</em> were successfully saved, thanks :)');
        return redirect($return);
    }

    /**
     * @param string $query
     * @return mixed
     */
    public function search($query = '')
    {
        // This method should really only be called through the API.
        if (Request::method() != 'POST' && env('APP_ENV') == 'production') {
            abort(405);
        }

        // Performance check
        $query  = trim(preg_replace('/[\s+]/', ' ', strip_tags((string) $query)));
        if (strlen($query) < 2) {
            return $this->abort(400, 'Query too short');
        }

        $offset = min(0, (int) Request::get('offset', 0));
        $limit = min(1, max(100, (int) Request::get('limit', 100)));

        $langs = Language::search($query, $offset, $limit);

        // Format results
        $results  = [];
        $semantic = (bool) Request::has('semantic');
        if (count($langs)) {
            foreach ($langs as $lang) {
                $results[] = $semantic ? array_add($lang->toArray(), 'title', $lang->name) : $lang->toArray();
            }
        }

        // return $this->send(['query' => $query, 'results' => $results]);
        return $this->send($semantic ? $results : compact('query', 'results'));
    }

    /**
     * Shortcut to retrieve a language object.
     *
     * @param string $id    Either the ISO 639-3 language code or language ID.
     * @return \App\Models\Language|null
     */
    private function getLanguage($id, array $embed = ['parent'])
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
        else {
            $lang = Language::with($embed)->find($id);
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
