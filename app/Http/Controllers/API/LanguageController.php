<?php

use \Symfony\Component\HttpFoundation\Response as SymRes;

/**
 * API interaction with Language table.
 */
class LanguageController extends ApiController
{
    /**
     *
     */
    public function search($query = '')
    {
        // Performance check
        $query  = trim(preg_replace('/[\s+]/', ' ', strip_tags((string) $query)));
        if (strlen($query) < 2) {
            return $this->abort(SymRes::HTTP_BAD_REQUEST, 'Query too short');
        }
        
        // Query the database
        $langs = Language::where('name', 'LIKE', '%'. $query .'%')
                    ->orWhere('code', 'LIKE', '%'. $query .'%')
                    ->orWhere('parent', 'LIKE', '%'. $query .'%')->get();
        
        // Format results
        $results    = array();
        if (count($langs)) {
            foreach ($langs as $lang) {
                $results[]  = array(
                    'code'          => $lang->code,
                    'name'          => $lang->getName(),
                    'altNames'      => $lang->getAltNames(true),
                    'parentCode'    => $lang->parent,
                    'parentName'    => $lang->getParam('parentName', ''),
                    'uri'           => $lang->getUri()
                );
            }
        }
        
        return $this->send(array('query' => $query, 'languages' => $results));
    }
}
