@extends('layouts.base')

@section('head.title')
	<title>Di Nkomo: in numbers.</title>
@stop

@section('body')
	@include('layouts.header')
	
	<section>
		<h1>Some stats</h1>
        There are over 7,000 languages spoken around the world today, 5% of which are spoken by about 95% of the population. It is estimated that a language <em>becomes extinct</em> every <em>two weeks</em>. In fact, about <em>35% are considered threatened</em> or on the road to extinction.<br /><br />
        
		Yada yada. (...). Yada
		
		<h2>Di Nkomo in numbers</h2>
		<div>
			Our database contains a total of <em>{{ $totalDefs }}</em> words, in
            <em>{{ $totalLangs }}</em> languages. Yada yada.
		</div>
	</section>

	@include('layouts.footer')
@stop
