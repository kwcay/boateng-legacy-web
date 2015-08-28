@extends('layouts.base')

@section('body')
	@include('layouts.header')

	<section>
		<h1>
		    Export Data
		    <br />

		    <small>
		        <a href="{{ route('admin.import') }}">&rarr; or click here to import</a>
		    </small>
        </h1>

		<h2>Languages</h2>
		    Available in
            <a href="{{ route('export.resource', ['resource' => 'language', 'format' => 'json']) }}">json</a> and
            <a href="{{ route('export.resource', ['resource' => 'language', 'format' => 'yaml']) }}">yaml</a> formats.

        <h2>Definitions</h2>
            Available in
            <a href="{{ route('export.resource', ['resource' => 'definition', 'format' => 'json']) }}">json</a>,
            <a href="{{ route('export.resource', ['resource' => 'definition', 'format' => 'yaml']) }}">yaml</a>,
            <a href="{{ route('export.resource', ['resource' => 'definition', 'format' => 'bgl']) }}">Babylon</a> and
            <a href="{{ route('export.resource', ['resource' => 'definition', 'format' => 'dict']) }}">StarDict (or Dictd)</a> formats.

        <h2>Users</h2>
            Available in
            <a href="{{ route('export.resource', ['resource' => 'user', 'format' => 'json']) }}">json</a> and
            <a href="{{ route('export.resource', ['resource' => 'user', 'format' => 'yaml']) }}">yaml</a> formats.

	</section>

	@include('layouts.footer')
@stop