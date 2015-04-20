@extends('layouts.base')

@section('body')
	@include('layouts.header')

	<section>
		<h1>Admin Stuff</h1>

		Export languages:
		    <a href="{{ route('export.language', ['format' => 'json']) }}">json</a> /
		    <a href="{{ route('export.language', ['format' => 'yaml']) }}">yaml</a>
		<br />

		Export definitions:
		    <a href="{{ route('export.definition', ['format' => 'json']) }}">json</a> /
		    <a href="{{ route('export.definition', ['format' => 'yaml']) }}">yaml</a> /
		    <a href="{{ route('export.definition', ['format' => 'bgl']) }}">Babylon</a> /
		    <a href="{{ route('export.definition', ['format' => 'dict']) }}">StarDict (or Dictd)</a>
		<br />

		Export users:
		    <a href="{{ route('export.user', ['format' => 'json']) }}">json</a> /
		    <a href="{{ route('export.user', ['format' => 'yaml']) }}">yaml</a>
		<br />
	</section>

	@include('layouts.footer')
@stop