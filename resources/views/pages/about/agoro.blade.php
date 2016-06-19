@extends('layouts.narrow')

@section('title', 'Agorɔ Learning App: Discover the hidden cultural gems in the languages of the world.')

@section('body')

	<section>
		<h1>
            @lang('branding.learning_app_title')
            <br>

            <small>
                @lang('branding.learning_app_tag_line')
            </small>
        </h1>

        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 text-center">
                Learn the basics of a local language and explore the cultural background
                that brought it to life. Then keep that information handy for the next time you might
                need it.
                <br>
                <br>

                <div class="emphasis">
                    Coming soon, stay tuned ;)
                </div>
            </div>
        </div>
	</section>

@stop
