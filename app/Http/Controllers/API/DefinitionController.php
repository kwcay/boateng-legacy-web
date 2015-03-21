<?php

use \Symfony\Component\HttpFoundation\Response as SymRes;

/**
 * API interaction with Definition table.
 */
class DefinitionController extends ApiController
{
    /**
     *
     */
    public function search($query = '')
    {
        // Performance check
        $query  = trim(preg_replace('/[\s+]/', ' ', strip_tags((string) $query)));
        if (strlen($query) < 3) {
            return $this->abort(SymRes::HTTP_BAD_REQUEST, 'Query too short');
        }
        
        // Query the database
        $defs = Definition::where('word', 'LIKE', '%'. $query .'%')
                    ->orWhere('translation', 'LIKE', '%'. $query .'%')
                    ->orWhere('meaning', 'LIKE', '%'. $query .'%')->get();
        
        // Format results
        $results  = array();
        if (count($defs)) {
            foreach ($defs as $def) {
                $results[]  = array(
                    'word'          => $def->getWord(),
                    'type'          => ($def->getParam('type') .'.'),
                    'alt'           => $def->getAltWords(true),
                    'translation'   => array('eng' => $def->getTranslation('eng')),
                    'meaning'       => array('eng' => $def->getMeaning('eng')),
                    'language'      => array(
                        'code'  => $def->getMainLanguage(true),
                        'name'  => $def->getMainLanguage(),
                        'uri'   => URL::to($def->getMainLanguage(true)),
                        'all'   => $def->language
                    ),
                    'uri'           => $def->getWordUri()
                );
            }
        }
        
        return $this->send(array('query' => $query, 'definitions' => $results));
    }
}
