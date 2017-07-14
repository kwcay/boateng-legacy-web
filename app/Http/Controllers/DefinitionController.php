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
     * Renders the definition view for the given ID.
     *
     * @param  string $id
     * @return Illuminate\Http\Response
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
     * @return Illuminate\Http\Response
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
     * @return Illuminate\Http\Response
     */
    public function edit($id)
    {
        // TODO: use authorization flow
        if (! Auth::check()) {
            abort(404);
        }

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

    public function form(array $details)
    {
        // TODO: handle this more gracefully
        if (! in_array($details['type'], ['word', 'expression'])) {
            abort(501, 'Unsupported Definition Type.');
        }

        // NOTE: this will be handled by a JS framework on the frontend.
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
                $details['subTypes'] = [];
                break;
        }

        return view('definition.'.$details['type'].'.form', $details);
    }

    /**
     * Updates a definition on the API.
     *
     * @param  string $id
     * @return Illuminate\Http\Response
     */
    public function update($id)
    {
        dd([
            'TODO: save definition',
            'id' => $id,
            'title' => $this->request->get('title'),
            'subType' => $this->request->get('subType'),
            'languages' => $this->request->get('languages'),
            'practical' => $this->request->get('practical'),
            'literal' => $this->request->get('literal'),
            'meaning' => $this->request->get('meaning'),
            'tags' => $this->request->get('tags'),
        ]);
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
