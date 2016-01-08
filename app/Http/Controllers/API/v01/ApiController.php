<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 * @version 0.1
 */
namespace App\Http\Controllers\API\v01;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Definition;
use App\Models\Definition\Name;
use App\Models\Definition\Phrase;
use App\Models\Definition\Poem;
use App\Models\Definition\Story;
use App\Models\Definition\Word;
use App\Models\Language;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Counts the # of resources.
     *
     * @param string $resource
     * @return
     */
    public function count($resource)
    {
        // Retrieve model.
        if (!$model = $this->getResourceModel($resource)) {
            return response('Invalid resource type.', 400);
        }

        return $model->count();
    }

    /**
     * Resource search.
     *
     * @param string $resourceName
     * @param string $query
     */
    public function search($resourceName, $query)
    {
        // Retrieve model.
        if (!$model = $this->getResourceModel($resourceName)) {
            return response('Invalid resource type.', 400);
        }

        // Retrieve search options.
        $options = [
            'offset' => $this->request->input('offset', 0),
            'limit' => $this->request->input('limit'),
            'lang' => $this->request->input('lang', ''),
            'method' => $this->request->input('method', 'default')
        ];

        // Perform search.
        return $model->search($query, $options);
    }

    /**
     * Queries the database for all resource types.
     *
     * @param string $query
     * @return \Illuminate\Support\Collection
     */
    public function searchAllResources($query)
    {
        // Lookup all definitions.
        $options = [];
        $results = Definition::search($query, $options);

        // Add languages.
        $results = $results->merge(Language::search($query));

        // Add countries.
        // ...

        return $results;
    }

    /**
     * Handles OPTIONS requests.
     */
    public function options()
    {
        return null;
    }

    /**
     * Helper method to determine the requested resource's type.
     *
     * @param string $resourceName
     * @return mixed
     */
    public function getResourceModel($resourceName)
    {
        // Retrieve definition model.
        $definitionTypes = array_flip(Definition::types());
        if (array_key_exists($resourceName, $definitionTypes)) {
            $model = Definition::getInstance($definitionTypes[$resourceName]);
        }

        // Or another model.
        else
        {
            switch (strtolower($resourceName))
            {
                case 'user':
                    $model = null;
                    break;

                case 'language':
                    $model = new Language;
                    break;

                default:
                    $model = null;
            }
        }

        return $model;
    }
}
