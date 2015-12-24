@extends('layouts.narrow')

@section('body')

	<section>
		<h1>Suggest a new language</h1>
        <br>

		<form class="form edit" method="post" action="{{ route('language.store') }}">
			{!! csrf_field() !!}

            <div class="row center">
                Add a language named
            </div>

			{{-- Name --}}
			<div class="row">
				<input type="text" name="name" class="text-input center" placeholder="your language" value="" required>
			</div>
            <br>

            <div class="row center">
                which has the 3-letter <a href="http://en.wikipedia.org/wiki/ISO_639-3" target="_blank">ISO-639-3</a> code
            </div>

			{{-- ISO-639-3 --}}
			<div class="row">
				<input type="text" name="code" class="en-text-input center" placeholder="3-letter code" value="" required>
			</div>
            <br>

            <div class="row center">
                to Di Nkɔmɔ.
            </div>
            <br>

        	{{-- Form actions --}}
			<div class="row center">
				<input type="submit" name="next" value="continue">
				<input type="submit" name="next" value="finish">
			</div>
		</form>
	</section>

@stop
