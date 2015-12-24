@extends('layouts.narrow')

@section('body')

	<h1>
        Suggest a new phrase or saying
        <br>

		<small>
			<a href="{{ route('language.create')  }}">
                &rarr; or click here to suggest a language
            </a>
		</small>
	</h1>
    <br>
    <br>

    <form
        class="edit form"
        method="post"
        name="definition"
        action="{{ route('definition.store') }}">

		{!! csrf_field() !!}
		<input type="hidden" name="type" value="{{ $type }}">
		<input type="hidden" name="relations[language][]" value="{{ $lang->code }}">

		{{-- Phrase --}}
		<div class="row center">
			In Development.
        </div>

		<!-- Form actions -->
        <br>
        <br>
		<div class="row center">
			<input type="submit" name="next" value="continue" disabled>
			<input type="submit" name="next" value="finish" disabled>
            <input type="button" name="cancel" value="return" onclick="return confirm('Cancel?') ? App.redirect('') : false;">
		</div>
	</form>

@stop
