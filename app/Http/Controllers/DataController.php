<?php namespace App\Http\Controllers;

use Request;
use App\Models\Language;
use App\Models\Definition;
use App\Http\Requests;
use App\Http\Controllers\Controller;

//use Illuminate\Http\Request;

class DataController extends Controller
{
    /**
     * Imports data into the database.
     *
     * Too much energy being spent on this.. code one feature at a time,
     * then move everything to Traits (or something else) later.
     *
     * @throws \Exception
     */
    public function import()
    {
        // Retrieve data
        $raw = strip_tags(trim(Request::input('data')));

        // Parse YAML
        $data = \Symfony\Component\Yaml\Yaml::parse($raw);

        // Create a unique data set
        $data = \Illuminate\Support\Collection::make(array_unique($data, SORT_REGULAR));

        // Import languges
        if (Request::input('resource') == 'language')
        {
            $data->each(function($item)
            {
                // Validate input data
                $test = Language::validate($item);
                $test->fails() ? \Redirect::to('/import')->withErrors($test) : null;

                // TODO: pull language saving methods from controller into model, so we can reuse them here.

                $lang = Language::firstOrCreate(['code' => $item['code']]);

                $lang->forceFill($item);

                $lang->save();
            });
        }


        \Session::push('messages', 'Import test !');
        return \Redirect::to('/import');
    }

    /**
     *
     */
	public function exportLanguages()
    {
        $data = Language::export();
    }

    /**
     *
     */
    public function exportDefinitions()
    {
        $data = Definition::export();
    }

}
