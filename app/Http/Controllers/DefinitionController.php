<?php
/**
 * Copyright Dora Boateng(TM) 2017, all rights reserved.
 */
namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class DefinitionController extends Controller
{
    /**
     * Supported definition types.
     *
     * @var array
     */
    protected $supportedTypes = [
        'word',
        'expression',
    ];

    /**
     * Displays the form to add a new definition.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $type       = 'word';
        $subType    = '';
        $languages  = [];
        $tags       = [];

        if ($this->request->has('languages')) {
            // TODO: validate languages
        }

        if ($this->request->has('tags')) {
            // TODO: add tags
        }

        return $this->form([
            'id'            => null,
            'type'          => $type,
            'subType'       => $subType,
            'title'         => '',
            'titleStr'      => $this->request->get('title', ''),
            'practical'     => $this->request->get('translation', ''),
            'literal'       => $this->request->get('literally', ''),
            'meaning'       => $this->request->get('meaning'),
            'languages'     => $languages,
            'tags'          => $tags,
        ]);
    }

    /**
     * Stores a new definition on the API.
     *
     * @return \Illluminate\Http\RedirectResponse
     */
    public function store()
    {
        return $this->save();
    }

    /**
     * Renders the definition view for the given ID.
     *
     * @param  string $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! $definition = $this->getDefinition($id)) {
            abort(404);
        }

        // Template path for edit form
        // TODO: use authorization flow instead.
        $form = null;

        if ($this->request->user()) {
            switch ($definition->type) {
                case 'word':
                    $form = 'forms.definition.word.modal';
                    break;

                case 'expression':
                    $form = 'forms.definition.expression.modal';
                    break;
            }
        }

        return view('definition.index')->with([
            'definition'    => $definition,
            'formTemplate'  => $form,
        ]);
    }

    /**
     * Retrieves a random definition in the specified language, and redirects to that definition's
     * page.
     *
     * @param  string $lang
     * @return \Illuminate\Http\RedirectResponse
     */
    public function random($lang = null)
    {
        $definition = $this->api->getRandomDefinition($lang);

        if (is_int($definition)) {
            // TODO: handle errors

            return redirect(route('home'));
        }

        return redirect(route('definition.show', $definition->uniqueId));
    }

    /**
     * Dispays the form to edit a defintion.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! $definition = $this->getdefinition($id)) {
            abort(404);
        }

        // Translations
        $practical  = isset($definition->translationData->eng->practical)
            ? $definition->translationData->eng->practical
            : '';
        $literal    = isset($definition->translationData->eng->literal)
            ? $definition->translationData->eng->literal
            : '';
        $meaning    = isset($definition->translationData->eng->meaning)
            ? $definition->translationData->eng->meaning
            : '';

        return $this->form([
            'id'            => $definition->uniqueId,
            'type'          => $definition->type,
            'subType'       => $definition->subType,
            'title'         => $definition->mainTitle,
            'titleStr'      => $definition->titleString,
            'practical'     => $practical,
            'literal'       => $literal,
            'meaning'       => $meaning,
            'languages'     => (array) $definition->languageList,
            'tags'          => (array) $definition->tagList,
        ]);
    }

    /**
     * Updates a definition on the API.
     *
     * @param  string $id
     * @return Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->save($id);
    }

    protected function form(array $details)
    {
        if (! in_array($details['type'], $this->supportedTypes)) {
            abort(501, 'Unsupported Definition Type.');
        }

        // NOTE: this will be handled by a JS framework on the frontend some day.
        $details['subTypes'] = [];
        switch ($details['type']) {
            case 'word':
                $details['subTypes'] = [
                    '[ part of speech ]',
                    'adjective',
                    'adverb',
                    'connective',
                    'exclamation',
                    'preposition',
                    'pronoun',
                    'noun',
                    'verb',
                    'intransitive verb',
                ];
                break;

            case 'expression':
                $details['subTypes'] = [
                    '[ expression types ]',
                    'expression'    => 'common expression',
                    'phrase'        => 'simple phrase',
                    'proverb'       => 'proverb or saying',
                ];
                break;
        }

        return view('definition.'.$details['type'].'.form', $details);
    }

    /**
     * Saves a definition resource on the API.
     *
     * @param  string $id
     * @return Illuminate\Http\Response
     */
    protected function save($id = null)
    {
        $this->validate($this->request, [
            'title'     => 'required|min:1',
            'type'      => 'required',
            'subType'   => 'required',
            'languages' => 'required',
            'practical' => 'required',
            'literal'   => '',
            'meaning'   => '',
            'tags'      => '',
        ]);

        $data = [
            'titleString'   => $this->request->get('title'),
            'subType'       => $this->request->get('subType'),
            'translationData' => [
                'eng' => [
                    'practical' => $this->request->get('practical'),
                    'literal'   => $this->request->get('literal'),
                    'meaning'   => $this->request->get('meaning'),
                ]
            ],
            'languageList'  => explode(',', $this->request->get('languages')),
            'tagList'       => explode(',', $this->request->get('tags')),
        ];

        $updated = $this->api->patch(
            $this->request->user()->getAccessToken(),
            'definitions/'.$id,
            $data
        );

        $response = redirect(route('definition.show', $id));

        if (! $updated) {
            $response->withErrors('Could not save definition');
        }

        return $response;
    }

    /**
     * Retrieves a definition by ID.
     *
     * @param  int  $id
     * @return stdClass
     */
    protected function getDefinition($id)
    {
        return $this->cache->remember('definition.'.$id, 60, function() use ($id) {
            return $this->api->getDefinition($id, [
                'languageList',
                'mainTitle',
                'titleString',
                'mainLanguage',     // DEPRECATED
                'translationData',
                'tagList',
            ]);
        });
    }

    protected function boot()
    {
        $this->middleware('auth')->only('create', 'edit', 'store', 'update', 'destroy');
    }
}
