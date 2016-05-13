@extends('layouts.narrow')

@section('body')

	<section>
		<h1>What would you like to do?</h1>

        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">

                &rarr;&nbsp; Thanks for asking. I'd like to
                <a href="{{ route('language.create') }}">suggest a new language</a>.
                <hr>

                &rarr;&nbsp; Thanks for asking. I'd like to suggest a new <em>word</em> in
                <form class="form" method="post">
                    <div class="row">
                        <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
                            <input
                                type="text"
                                id="languageForWord"
                                class="text-input center"
                                placeholder="your language"
                                autocomplete="off">
                            </div>
                        </div>
                </form>
                <hr>

                &rarr;&nbsp; Thanks for asking. I'd like to suggest a new <em>saying or expression</em> in
                <form class="form" method="post">
                    <div class="row">
                        <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
                            <input
                                type="text"
                                id="languageForExpression"
                                class="text-input center"
                                placeholder="your language"
                                autocomplete="off">
                            </div>
                        </div>
                </form>
                <hr>

                &rarr;&nbsp; Thanks for asking. I'd like to
                <a href="mailto:&#102;&#114;&#97;&#110;&#107;&#64;&#102;&#114;&#110;&#107;&#46;&#99;&#97;">
                    share some feedback
                </a> or say nice things to the author.
                <hr>

                &rarr;&nbsp; Thanks for asking. <a href="{{ route('about') }}">I don't know</a>.
            </div>
        </div>
	</section>

    <script type="text/javascript">

    $('#languageForWord').langSearch({
        onItemAdd: function(code) {
            App.redirect(code + '/+word');
        }
    });

    $('#languageForExpression').langSearch({
        onItemAdd: function(code) {
            App.redirect(code + '/+expression');
        }
    });

    </script>

@stop
