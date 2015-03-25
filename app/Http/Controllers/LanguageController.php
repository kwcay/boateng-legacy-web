<?php
namespace App\Http\Controllers;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 *
 */
class LanguageController extends Controller {


    
    /**
     * Language page
     */
    public function __TO_BE_RE_WORKED__showLangPage($code)
    {
        // Find language by code
        if (!$lang = Language::findByCode($code)) {
            throw new NotFoundHttpException;
        }
        
        return view('pages.lang')->withLang($lang);
    }
    
	/**
	 * Displays the language form.
     *
	 * @param  string   $code   ISO 639-3 language code
	 * @return \Illuminate\View\View
	 */
	public function __TO_BE_RE_WORKED__showLanguageForm($id = '')
	{
		// Load existing language by code
		if (strlen($id) == 3 || strlen($id) == 7) {
			$lang	= Language::findByCode($id);
			$form	= 'forms.language.default';
		}
        
        // Load by ID
        elseif (strlen($id) >= 8) {
			$lang	= Language::find($id);
			$form	= 'forms.language.default';
        }
		
		// New language
		elseif (empty($id))
        {
			$lang	= new Language;
            
            // Set some defaults
            if ($name = Input::get('name', Input::old('name'))) {
                $lang->setName($name);
            }
            if ($alt = Input::old('alt')) {
                $lang->setAltName($alt);
            }
            if ($code = Input::get('code', Input::old('code'))) {
                $lang->code     = preg_replace('/[^a-z\-]/', '', $code);
            }
            if ($parent = Input::get('parent', Input::old('parent'))) {
                if ($parentObj = Language::findByCode($parent)) {
                    $lang->parent   = $parentObj->code;
                    $lang->setParam('parentName', $parentObj->getName());
                }
            }
            if ($countries = Input::old('countries')) {
                $lang->countries    = preg_replace('/[^a-z,]/', '', $countries);
            }
            if ($desc = Input::old('desc')) {
                $lang->desc     = strip_tags($desc);
            }
            
			//$form	= 'forms.language.walkthrough';
			$form	= 'forms.language.default';
		}
        
        //
        else {
            throw new \LogicException('Invalid identifier');
        }
        
        // 
        if (!$lang) {
            throw new NotFoundHttpException;
        }
		
		return view($form)->withLang($lang);
	}
}
