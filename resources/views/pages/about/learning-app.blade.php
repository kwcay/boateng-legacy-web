@extends('layouts.narrow')

@section('title', trans('branding.learning_app_title') .': '. trans('branding.learning_app_tag_line'))
@section('description', trans('branding.learning_app_tag_line'))
@section('keywords', 'learning, app, language, game')

@section('body')

	<section>
		<h1>
            @lang('branding.learning_app_title')
            <br>

            <small>
                @lang('branding.learning_app_tag_line_span')
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

    <script type="text/javascript">

    $(function(){
        $('.learning-app-lang').typed({
            strings: {!! json_encode(collect($topLanguages)->map(function($lang) {
                return $lang['name'];
            })) !!},
            typeSpeed: 40,
            backSpeed: 20,
            backDelay: 4000,
            loop: true,
        });
    });
    
    </script>

@stop
