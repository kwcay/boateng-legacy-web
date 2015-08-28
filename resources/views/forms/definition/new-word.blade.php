@extends('layouts.base')

@section('body')
	@include('partials.header')

	<section>
		<h1>
            Suggest a new word<br />
			<small>
				<a href="{{ route('language.create')  }}">&rarr; or click here to suggest a language</a>
			</small>
		</h1>
        <br />
        <br />

        <form class="form edit" method="post" name="definition" action="{{ route('definition.store') }}">

			<!-- Word -->
			<div class="row">
				<input type="text" name="title" class="text-input center" placeholder="your word" value="" />
			</div>

            <div class="row center">
                is a word in <em>{{ $lang->name }}</em> that means
            </div>

            <!-- Translation -->
            <div class="row">
                <input type="text" name="translation[en]" class="en-text-input center" placeholder="your translation" value="" />
            </div>

            <div class="row center">
                in English.
            </div>

			<!-- Form actions -->
            <br />
            <br />
			<div class="row center">
				<input type="submit" name="finish" value="continue" />
				<input type="button" name="cancel" value="return" onclick="return confirm('Cancel editing?') ? App.redirect('') : false;" />
			</div>

			{!! Form::token() !!}
            <input type="hidden" name="language" value="">
		</form>
	</section>

	@include('partials.footer')
@stop
