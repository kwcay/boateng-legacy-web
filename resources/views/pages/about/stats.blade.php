@extends('layouts.narrow')

@section('title', 'Di Nkomo: in numbers.')

@section('body')

	<section>
		<h1>Some stats</h1>
        There are over 7,000 languages spoken around the world today, 5% of which are spoken by
        about 95% of the population. It is estimated that a language <em>becomes extinct</em> every
        <em>two weeks</em>. In fact, about <em>35% are considered threatened</em> or on the road to
        extinction.<br /><br />

		<h2>Di Nkomo in numbers</h2>
		Di Nkɔmɔ has <em>{{ $totalDefs }}</em> definitions, in <em>{{ $totalLangs }}</em> languages
        to date. <a href="#" onclick="return App.openDialog('resource');">Click here</a> to contribute!
	</section>

@stop
