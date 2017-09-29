<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * @todo Check for an exact title match
     */
    public function checkTitle($lang, $title)
    {
        if (! $title = trim($title)) {
            return [];
        }

        return $this->api->get('definitions/search', [
            'q'         => $title,
            'limit'     => 1,
            'format'    => 'compact',
        ]);
    }
}
