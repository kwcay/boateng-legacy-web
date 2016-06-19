@extends('layouts.narrow')

@section('title', 'Di Nkɔmɔ API: An API for the languages &amp; cultures of the world.')

@section('body')

	<section>
		<h1>
            @lang('branding.api_title')
            <br>

            <small>
                @lang('branding.api_tag_line')
            </small>
        </h1>

        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 text-center">
                Access our library of definitions for words and sayings in local languages.
                <br>
                <br>

                <div class="emphasis">
                    Coming soon, stay tuned ;)
                </div>

                {{-- https://developer.github.com/v3/ --}}
            </div>
        </div>
	</section>

@stop
