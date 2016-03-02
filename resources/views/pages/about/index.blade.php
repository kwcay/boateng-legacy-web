@extends('layouts.narrow')

@section('title', 'About Di Nkomo')

@section('body')

	<section>
        <h1>
            <span ng-hide="language">Di Nkɔmɔ</span>

            <br>
            <small>A Cultural Reference</small>
        </h1>

        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 text-center">
                Di Nkɔmɔ is a free, online reference for the cultures of the world.
                <br>
                <br>

                It started as a web-based dictionary for non-dominant languages (95% of all languages
                <a href="{{ route('stats') }}">fall within that category</a>). Yet, language by
                itself is really only half of something that's both bigger and beautiful.
                <br>
                <br>

                Combined with a cultural context, it begins to tell the story of a people.
                <br>
                <br>

                Language is, truly, a bearer and medium through which culture is experienced and
                transmitted. <a href="{{ route('story') }}">My hope</a> is that Di Nkɔmɔ can help
                preserve and make sense of these treasures.
            </div>
        </div>
	</section>

@stop
