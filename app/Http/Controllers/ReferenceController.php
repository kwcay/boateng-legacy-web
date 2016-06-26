<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Http\Controllers;

use Auth;
use Request;
use Session;
use App\Models\Alphabet;

class ReferenceController extends Controller
{
    /**
     *
     */
    protected $name = 'reference';

    /**
     *
     */
    protected $defaultQueryLimit = 20;

    /**
     *
     */
    protected $supportedOrderColumns = [
        'id' => 'ID',
        'type' => 'Type',
        'string' => 'Summary',
    ];

    /**
     *
     */
    protected $defaultOrderColumn = 'id';

    /**
     *
     */
    protected $defaultOrderDirection = 'desc';

	/**
	 * Displays the form to add a new alphabet.
	 *
	 * @return Response
	 */
	public function walkthrough() {
        return view('forms.reference.walkthrough');
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
}
