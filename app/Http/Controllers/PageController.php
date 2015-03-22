<?php
namespace App\Http\Controllers;
use \App\Models\Definition;

/**
 *
 */
class PageController extends Controller
{
	/**
	 * Main landing page.
	 */
	public function home() {
		return view('pages.home', [
            'wordOfTheDay' => Definition::where('state', '>', '0')->orderByRaw('RAND()')->first()
        ]);
	}

	/**
	 * About page
	 */
	public function getAboutPage() {
		return view('pages.about');
	}

	/**
	 * "Di Nkomo: in numbers"
	 */
	public function getStatsPage() {
		return view('pages.stats');
	}

	/**
	 * API description page
	 */
	public function getApiPage() {
		return view('pages.api');
	}
    
    /**
     * Login form
     */
    public function showLoginForm()
    {
        // Check if user is already logged in
        if (false) {
        
        }
        
        return view('forms.login');
    }
    
    /**
     * Language page
     */
    public function showLangPage($code)
    {
        // Find language by code
        if (!$lang = Language::findByCode($code)) {
            throw new NotFoundHttpException;
        }
        
        return view('pages.lang')->withLang($lang);
    }
    
    /**
     * Word page
     */
    public function showWordPage($code, $raw)
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
	 * Edit page. Displays the right form according to request.
     *
	 * @param  string $id Identifier for element to edit.
	 * @return \Illuminate\View\View
	 */
	public function showEditPage($id = '')
    {
        switch(Input::get('what'))
        {
            case 'lang':
            case 'language':
                $view   = $this->showLanguageForm($id);
                break;
            
            case 'def':
            case 'definition':
                $view   = $this->showDefinitionForm($id);
                break;
            
            default:
                $langID = (strlen($id) == 3 || strlen($id) == 7);
                $view   = $langID ? $this->showLanguageForm($id) : $this->showDefinitionForm($id);
                break;
        }
        
		return $view;
	}

	/**
	 * Displays the language form.
     *
	 * @param  string   $code   ISO 639-3 language code
	 * @return \Illuminate\View\View
	 */
	public function showLanguageForm($id = '')
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

	/**
	 * Displays the definition form.
     *
	 * @param  string $id Identifier for the definition
	 * @return \Illuminate\View\View
	 */
	public function showDefinitionForm($id = '')
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
