<?php
namespace App\Http\Controllers;
use Illuminate\Support\MessageBag;

/**
 * Controls editing of resources
 */
class EditController extends Controller
{
    public function __construct()
    {
        // Apply Cross-site request forgery filter before all methods
        $this->beforeFilter('csrf', array('on' => 'post'));
    }
    
	/**
	 * Shortcut to route execute the right method to save the given resource.
	 * @param string $what Type of resource.
	 */
    public function saveRes($what = null) {
        return $what == 'lang' ? $this->saveLanguage() : $this->saveDefinition();
    }
    
    public function saveLanguage($data = null)
    {
        // Retrieve existing language record
        $data   = $data ? $data : Input::all();
        if (array_key_exists('id', $data)) {
            $lang   = Language::findOrFail($data['id']);
            $rdir   = $lang->getEditUri(false);
        }
        
        // Or create an instance to work with
        elseif (array_key_exists('code', $data))  {
            //$lang   = Language::create(array('code' => $data['code']));
            $lang   = new Language;
            $lang->code = $data['code'];
            $rdir   = '/edit?what=lang';
        }
        
        // Catch any logical errors...
        else {
            throw new \LogicException('Invalid language data');
        }
        
        // Validate input data
        $test   = Validator::make($data, Language::$validationRules);
        if ($test->fails())
        {
            // Flash input data to session
            Input::flashExcept('_token');
            
            // Return to form
            return Redirect::to($rdir)->withErrors($test);
        }
        
        // Existing language
        
        // Main details
        $lang->setName($data['name']);
        $lang->setAltName($data['alt']);
        $lang->countries    = implode(',', $data['countries']);
        $lang->countries    = preg_replace('/[^A-Z,]/', '', $lang->countries);
        $lang->desc         = trim(strip_tags($data['desc']));
        
        // Parent language details
        if (strlen($data['parent']) >= 3 && $parent = Language::findByCode($data['parent'])) {
            $lang->parent   = $parent->code;
            $lang->setParam('parentName', $parent->getName());
        } else {
            $lang->parent   = '';
            $lang->setParam('parentName', '');
        }
        
        $lang->save();
        
        Session::push('messages', 'The details for <em>'. $lang->getName() .'</em> were successfully saved, thanks :)');
        return Redirect::to($lang->getUri(false));
    }
    
    public function saveDefinition($data = null)
    {
        // Retrieve definition record
        $data   = $data ? $data : Input::all();
        if (array_key_exists('id', $data)) {
            $def    = Definition::findOrFail($data['id']);
            $rdir   = $def->getEditUri(false);
        }
        
        // Or create a new instance, to be created soon
        else {
            $def    = new Definition;
            $rdir   = '/edit';
        }
        
        // Validate input data
        $test   = Validator::make($data, Definition::$validationRules);
        if ($test->fails())
        {
            // Flash input data to session
            Input::flashExcept('_token');
            
            // Return to form
            return Redirect::to($rdir)->withErrors($test);
        }
        
        // Check languages, suggest other languages (esp. parents)
        $codes  = explode(',', $data['language']);
        foreach ($codes as $code)
        {
            if ($lang = Language::findByCode($code))
            {
                // Check if the language has a parent, and
                // whether that parent is already in the list.
                if (strlen($lang->parent) >= 3 && !in_array($lang->parent, $codes))
                {
                    array_push($codes, $lang->parent);
                    
                    // Notify the user of the change
                    Session::push('messages', '<em>'. $lang->getParam('parentName') .'</em> is the parent language for <em>'. $lang->getName() .'</em>, and was added to the list of languages the word <em>'. $data['word'] .'</em> exists in.');
                }
            }
        }
        
        // Main details
        $def->setWord($data['word']);
        $def->setAltWord($data['alt']);
        $def->language  = implode(',', $codes);
        $def->setParam('type', $data['type']);
        $def->state     = 1;
        
        // Translations
        foreach ($data['translation'] as $lang => $translation) {
            $def->setTranslation($lang, trim($translation));
        }
        
        // Meanings
        foreach ($data['meaning'] as $lang => $meaning) {
            $def->setMeaning($lang, trim($meaning));
        }
        
        $def->save();
        
        Session::push('messages', 'The details for <em>'. $def->getWord() .'</em> were successfully saved, thanks :)');
        return Redirect::to($def->getWordUri(false));
    }
}
