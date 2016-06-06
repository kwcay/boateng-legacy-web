@extends('layouts.narrow')

@section('body')

	<section>
		<h1>
            Suggest a new language
            <br>

            <small>
                And help improve @lang('branding.name') for everyone.
            </small>
        </h1>

		<form class="form edit" method="post" action="{{ route('language.store') }}">
			{!! csrf_field() !!}

            <div class="row center">
                Add a language named
            </div>

			{{-- Name --}}
			<div class="row">
				<div class="col-lg-10 col-lg-offset-1">
				    <input
                        type="text"
                        name="name"
                        class="text-input center"
                        placeholder="your language"
                        value=""
                        autocomplete="false"
                        required>
				</div>
			</div>
            <br>

            <div class="row center">
                which has the 3-letter
                <a href="http://en.wikipedia.org/wiki/ISO_639-3" target="_blank">ISO-639-3</a> code
            </div>

			{{-- ISO-639-3 --}}
			<div class="row">
				<div class="col-sm-6 col-sm-offset-3 col-lg-4 col-lg-offset-4">
				    <input
                        type="text"
                        name="code"
                        class="en-text-input center"
                        placeholder="3-letter code"
                        value=""
                        autocomplete="false"
                        required>
				</div>
			</div>
            <br>

            <div class="row center">
                to Di Nkɔmɔ.
            </div>
            <br>

        	{{-- Form actions --}}
			<div class="row center">
				<input type="submit" name="return" value="continue">
				<input type="submit" name="return" value="finish">
			</div>
		</form>
	</section>

@stop
