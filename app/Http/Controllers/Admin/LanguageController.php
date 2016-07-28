<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved.
 *
 * @todo    Deprecate for App\Http\Controllers\LanguageController ?
 */
namespace App\Http\Controllers\Admin;

use Session;
use Redirect;
use App\Models\Language;
use App\Http\Controllers\LanguageController as Controller;

/**
 * @abstract Admin controller for the Language resource.
 */
class LanguageController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @todo Review method
     * @param int $id
     * @return Response
     */
    public function update($id)
    {
        // Retrieve the language object.
        if (! $lang = Language::find($id)) {
            abort(404);
        }

        $this->validate($this->request, (new Language)->validationRules);

        // Update attributes.
        $lang->fill($this->request->only([
            'parentCode',
            'name',
            'transliteration',
            'altNames',
        ]));

        if (! $lang->save()) {
            abort(500);
        }

        // Update alphabets.
        $alphabets = $this->getAlphabets($this->request->get('alphabets', ''));
        $alphabetIDs = $alphabets->map(function ($item) {
            return $item->id;
        })->toArray();

        $lang->alphabets()->sync($alphabetIDs);

        // Update countries.
        $countries = $this->getCountries($this->request->get('countries', ''));
        $countryIDs = $countries->map(function ($item) {
            return $item->id;
        })->toArray();

        $lang->countries()->sync($countryIDs);

        // Send success message to client, and a thank you.
        Session::push('messages', 'The details for <em>'.$lang->name.
            '</em> were successfully saved, thanks :)');

        // Return URI
        switch ($this->request->get('return')) {
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
     * @todo Review method
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        // Retrieve the language model.
        if (! $lang = Language::find($id)) {
            throw new \Exception(trans('errors.resource_not_found'), 404);
        }

        // Delete record
        Session::push('messages', '<em>'.$lang->name.'</em> has been succesfully deleted.');
        $lang->delete();

        // Return URI
        switch ($this->request->get('return')) {
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
}
