@extends('layouts.narrow')

@section('title', 'Di Nkomo in numbers.')

@section('body')

	<section>
		<h1>Di Nkɔmɔ in numbers</h1>

        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 text-center">
                There are over 7,000 languages spoken around the world today, 5% of which are spoken by
                about 95% of the population. In other words, a handful of the world's languages has spread
                to most of the human population. Meanwhile, it is estimated that a language
                becomes <em>extinct</em> every <em>two weeks</em>. In fact, about
                <em>35% are considered threatened</em> or on the road to extinction
                (<a href="https://www.ethnologue.com/statistics" target="_blank">source: Ethnologue</a>).
                <br>
                <br>

                Language is, truly, a bearer and medium through which culture is experienced and transmitted.
                It might not be enough to save all these treasures, but the {{ $totalDefs }} definitions
                in {{ $totalLangs }} languages stored in Di Nkɔmɔ are safe and sound. You're welcome :)
                Feel free to <a href="{{ route('contribute') }}">add your own</a>.
            </div>
        </div>
	</section>

@stop
