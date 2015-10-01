<?php
/**
 * @file    ApiController.php
 * @brief   ...
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Definition;
use App\Models\Definition\Name;
use App\Models\Definition\Phrase;
use App\Models\Definition\Poem;
use App\Models\Definition\Story;
use App\Models\Definition\Word;

class ApiController extends Controller
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * General search.
     *
     * @param string $resource
     * @param string $query
     */
    public function search($resource, $query)
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
                    return $this->abort(501);

                default:
                    return $this->abort(400, 'Invalid resource type.');
            }
        }

        if (!$model) {
            return $this->abort(500, 'Could not load model.');
        }

        // Retrieve search options.
        $options = [
            'lang' => $this->request->input('lang', ''),
            'method' => $this->request->input('method', 'fulltext')
        ];

        // Perform search.
        return $model->search($query, $options);
    }
}
