@extends('layouts.narrow')

@section('body')

	<h1>Admin Stuff</h1>

    <h2>Stats</h2>
    <ul>
        <li>Languages: {{ App\Models\Language::count() }}</li>
        <li>Definitions: {{ App\Models\Definition::count() }}</li>
        <li>Translations: {{ App\Models\Translation::count() }}</li>
    </ul>

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
