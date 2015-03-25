<?php
namespace App\Http\Controllers;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 *
 */
class DefinitionController extends Controller {

	//
    
    /**
     * Word page
     */
    public function __TO_BE_RE_WORKED__showWordPage($code, $raw)
    {
        // Find language by code
        if (!$lang = Language::findByCode($code)) {
            throw new NotFoundHttpException;
        }
        
        // Check user input
        $word   = trim(preg_replace('/[\s]+/', '_', strip_tags($raw)));
        if (strlen($word) < 2) {
            throw new NotFoundHttpException;
        } elseif ($word != $raw) {
            return Redirect::to($lang->code .'/'. $word);
        }
        
        // Find words matching the query
        $word   = str_replace('_', ' ', $word);
        $wWord  = '(word = :a OR word LIKE :b or word LIKE :c or word LIKE :d)';
        $wLang  = '(language = :w OR language LIKE :x or language LIKE :y or language LIKE :z)';
        $words  = Definition::whereRaw($wWord .' AND '. $wLang, array(
            ':a' => $word,
            ':b' => $word .',%',
            ':c' => '%,'. $word .',%',
            ':d' => '%,'. $word,
            ':w' => $lang->code,
            ':x' => $lang->code .',%',
            ':y' => '%,'. $lang->code .',%',
            ':z' => '%,'. $lang->code
        ))->get();
        
        if (!count($words)) {
            throw new NotFoundHttpException;
        }
        
        return view('pages.word', array(
            'lang'  => $lang,
            'query' => $word,
            'words' => $words
        ));
    }

	/**
	 * Displays the definition form.
     *
	 * @param  string $id Identifier for the definition
	 * @return \Illuminate\View\View
	 */
	public function __TO_BE_RE_WORKED__showDefinitionForm($id = '')
	{
		// Existing definition
		if (strlen($id) >= 8)
        {
			$def	= Definition::findOrFail($id);
            
            // Create languages options for selectize
            $lso = array();
            $langs  = Language::whereIn('code', explode(',', $def->language))->get();
            foreach ($langs as $lang) {
                $obj = new stdClass;
                $obj->code = $lang->code;
                $obj->name = $lang->getName();
                array_push($lso, $obj);
            }
            
            // Set proper layout
			$form	= 'forms.definition.default';
		}
		
		// New definition
		elseif (empty($id))
        {
			$def	= new Definition;
            $lso    = array();
            
            // Set some defaults
            if ($word = Input::get('word', Input::old('word'))) {
                $def->setWord($word);
            }
            if ($alt = Input::old('alt')) {
                $def->setAltWord($alt);
            }
            if ($type = Input::old('type')) {
                $def->setParam('type', $type);
            }
            if ($lang = Input::get('lang', Input::old('lang'))) {
                $def->language  = preg_replace('/[^a-z, ]/', '', $lang);
            }
            if ($translation = Input::old('translation')) {
                foreach ($translation as $lang => $trans) {
                    $def->setTranslation($lang, $trans);
                }
            }
            if ($meaning = Input::old('meaning')) {
                foreach ($meaning as $lang => $mean) {
                    $def->setMeaning($lang, $mean);
                }
            }
            
			//$form	= 'forms.definition-walkthrough';
			$form	= 'forms.definition.default';
		}
        
        //
        else {
            throw new \LogicException('Invalid identifier');
        }
		
		return view($form, array(
            'def'       => $def,
            'options'   => $lso
        ));
	}
}
