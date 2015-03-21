@extends('layouts.base')

@section('head.title')
	<title>Di Nkomo API</title>
@stop

@section('body')
	@include('layouts.header')
	
	<section>
		<h1>Application Program Interface</h1>
        Is what they call it.
        
        {{-- https://developer.github.com/v3/ --}}
        
        <h2>Overview</h2>
        Yada yada. Search words through:<br />
        <code>{{ URL::route('api.definition.index') }}</code>
	</section>

	@include('layouts.footer')
@stop
