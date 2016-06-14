@extends('layouts.narrow')

@section('body')

	<section>
		<h1>
            Suggest a new alphabet
            <br>

            <small>
                And help improve @lang('branding.name') for everyone.
            </small>
        </h1>

		<form class="form edit" method="post" action="{{ route('alphabet.store') }}">
			{!! csrf_field() !!}

            <div class="row center">
                Add an alphabet named
            </div>

			{{-- Name --}}
			<div class="row">
				<div class="col-lg-10 col-lg-offset-1">
				    <input
                        type="text"
                        name="name"
                        class="text-input center"
                        placeholder="your alphabet"
                        value=""
                        autocomplete="false"
                        required="required">
				</div>
			</div>
            <br>

            <div class="row center">
                with the
                <a href="http://en.wikipedia.org/wiki/ISO_15924" target="_blank">ISO 15924</a>
                based code
            </div>

			{{-- ISO 15924 based code --}}
			<div class="row">
				<div class="col-sm-6 col-sm-offset-3 col-lg-4 col-lg-offset-4">
				    <input
                        type="text"
                        name="code"
                        class="en-text-input center"
                        placeholder="e.g. twi-Latn"
                        value=""
                        autocomplete="false"
                        required="required">
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
			</div>
		</form>
	</section>

@stop
