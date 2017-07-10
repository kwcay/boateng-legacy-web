<?php
/**
 * Copyright Dora Boateng(TM) 2017, all rights reserved.
 */
namespace App\Http\Controllers;

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
}
