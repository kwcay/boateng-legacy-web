@extends('layouts.narrow')

@section('title', 'About Di Nkomo')

@section('body')

	<section>
        <h1>
            <span ng-hide="language">Di Nkɔmɔ</span>

            <br>
            <small>A Cultural Collection</small>
        </h1>

        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 text-center">
                Di Nkɔmɔ started as a web-based dictionary for the non-dominant languages of the
                world. 95% of all languages
                <a href="{{ route('stats') }}">fall within that category</a>.
                <br>
                <br>

                The present goal is to turn it into a
                <a href="{{ route('story') }}">cultural repository</a> that might serve as a free
                reference for the languages and other treasures of the world.
            </div>
        </div>
	</section>

@stop
