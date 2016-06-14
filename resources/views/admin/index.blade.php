@extends('layouts.narrow')

@section('body')

	<h1>Admin Stuff</h1>

    <h2>Overview</h2>
    <ul>
        <li>
            <a href="{{ route('admin.definition.index') }}">
                {{ number_format(App\Models\Definition::count()) }} definitions
            </a>
        </li>
        <li>
            <a href="{{ route('admin.tag.index') }}">
                {{ number_format(App\Models\Tag::count()) }} tags
            </a>
        </li>
        <li>
            <a href="{{ route('admin.language.index') }}">
                {{ number_format(App\Models\Language::count()) }} languages
            </a>
        </li>
        <li>
            <a href="{{ route('admin.alphabet.index') }}">
                {{ number_format(App\Models\Alphabet::count()) }} alphabets
            </a>
        </li>
        <li>
            <a href="{{ route('admin.country.index') }}">
                {{ number_format(App\Models\Country::count()) }} countries
            </a>
        </li>
    </ul>

    <h2>Import/Export</h2>
	<ul>
	    <li>
	        <a href="{{ route('admin.import') }}">Import data.</a>
	    </li>
	    <li>
	        <a href="{{ route('admin.backup') }}">Backups.</a>
	    </li>
	</ul>

@stop
