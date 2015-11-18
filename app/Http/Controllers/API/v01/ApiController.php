<?php
/**
 * @file    ApiController.php
 * @brief   ...
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
            return $this->abort(500, 'Invalid resource type.');
        }

        return $this->send($model->count());
    }

    /**
     * Resource search.
     *
     * @param string $resource
     * @param string $query
     */
    public function search($resource, $query)
    {
        // Retrieve model.
        if (!$model = $this->getResourceModel($resource)) {
            return $this->abort(400, 'Invalid resource type.');
        }

        // Retrieve search options.
        $options = [
            'lang' => $this->request->input('lang', ''),
            'method' => $this->request->input('method', 'fulltext')
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
        return response('OK', 200);
    }

    /**
     * Helper method to determine the requested resource's type.
     *
     * @param string $resource
     * @return mixed
     */
    public function getResourceModel($resource)
    {
        // Retrieve definition model.
        $definitionTypes = array_flip(Definition::types());
        if (array_key_exists($resource, $definitionTypes)) {
            $model = Definition::getInstance($definitionTypes[$resource]);
        }

        // Or another model.
        else
        {
            switch (strtolower($resource))
            {
                case 'user':
                case 'language':
                    $model = null;
                    break;

                default:
                    $model = null;
            }
        }

        return $model;
    }
}
