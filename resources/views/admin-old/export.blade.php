@extends('layouts.narrow')

@section('body')

	<h1>
	    Export Data
	    <br>

	    <small>
	        <a href="{{ route('admin.import') }}">&rarr; or click here to import</a>
	    </small>
    </h1>

	<h2>Alphabets</h2>
	    Available in
        <a href="{{ route('admin.export', ['resource' => 'alphabet', 'format' => 'json']) }}">json</a> and
        <a href="{{ route('admin.export', ['resource' => 'alphabet', 'format' => 'yaml']) }}">yaml</a> formats

	<h2>Countries</h2>
	    Available in
        <a href="{{ route('admin.export', ['resource' => 'country', 'format' => 'json']) }}">json</a> and
        <a href="{{ route('admin.export', ['resource' => 'country', 'format' => 'yaml']) }}">yaml</a> formats.

    <h2>Definitions</h2>
        Available in
        <a href="{{ route('admin.export', ['resource' => 'definition', 'format' => 'json']) }}">json</a>,
        <a href="{{ route('admin.export', ['resource' => 'definition', 'format' => 'yaml']) }}">yaml</a>,
        <a href="{{ route('admin.export', ['resource' => 'definition', 'format' => 'bgl']) }}">Babylon</a> and
        <a href="{{ route('admin.export', ['resource' => 'definition', 'format' => 'dict']) }}">StarDict (or Dictd)</a> formats.

	<h2>Languages</h2>
	    Available in
        <a href="{{ route('admin.export', ['resource' => 'language', 'format' => 'json']) }}">json</a> and
        <a href="{{ route('admin.export', ['resource' => 'language', 'format' => 'yaml']) }}">yaml</a> formats.

@stop
