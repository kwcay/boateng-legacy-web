@extends('layouts.narrow')

@section('body')

	<h1>Admin Stuff</h1>

    <h2>Overview</h2>

    <em>{{ App\Models\Translation::count() }}</em> translations and
    <em>{{ App\Models\Definition::count() }}</em>
        <a href="{{ route('admin.definitions.index') }}">definitions</a> in
    <em>{{ App\Models\Language::count() }}</em>
        <a href="{{ route('admin.languages.index') }}">languages</a>, written in
    <em>{{ App\Models\Alphabet::count() }}</em>
        <a href="{{ route('admin.alphabets.index') }}">alphabets</a> spread accross
    <em>{{ App\Models\Country::count() }}</em>
        <a href="{{ route('admin.countries.index') }}">countries</a>.

    <h2>Import/Export</h2>
	<ul>
	    <li>
	        <a href="{{ route('admin.import') }}">Import data.</a>
	    </li>
	    <li>
	        <a href="{{ route('admin.export') }}">Export data.</a>
	    </li>
	    <li>
	        <a href="{{ route('admin.backup') }}">Backups.</a>
	    </li>
	</ul>

@stop
