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
        $definition = $this->cache->remember('definition.'.$id, 60, function() use ($id) {
            return $this->api->getDefinition($id, [
                'languageList',
                'mainTitle',
                'titleString',
                'mainLanguage',     // DEPRECATED
                'translationData',
                'tagList',
            ]);
        });

        if (! $definition) {
            abort(404);
        }

        // Template path for edit form
        // TODO: use authorization flow insread.
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

        return redirect(route('definition', $definition->uniqueId));
    }

    /**
     *
     */
    public function edit($id)
    {
        // TODO
        if (! Auth::check()) {
            abort(404);
        }

        if (! $definition = $this->getdefinition($id)) {
            abort(404);
        }

        // TODO
        if (! in_array($definition->type, array('word'))) {
            abort(501, 'Unsupported Definition Type.');
        }

        // TODO: use helper to generate select field.
        $subTypes = [];
        switch ($definition->type) {
            case 'word':
                $subTypes = [
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
        }

        return view('definition.'.$definition->type.'.edit', [
            'definition'    => $definition,
            'subTypes'      => $subTypes,
        ]);
    }

    /**
     * Retrieves a definition by ID.
     *
     * @param  int $id
     * @return stdClass|null
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
}
