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
     *
     */
    public function import()
    {
        // Retrieve data
        switch (Request::input('medium'))
        {
            case 'plain':
                $raw = strip_tags(trim(Request::input('data')));
                break;

            case 'file':
            case 'archive':
            default:
                throw new \Exception('Unsupported import medium.');
        }

        switch (Request::input('resource'))
        {
            case 'lang':
            case 'languages':
                $data = Language::importFromFormat($raw, Request::input('format'));
                dd($data);
                break;

            case 'def':
            case 'definition':
                $data = Definition::importFromFormat($raw, Request::input('format'));
                dd($data);
                break;

            case 'user':
            default:
                dd(Request::input('resource'));
                throw new \Exception('Invalid resource type.');
        }
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
