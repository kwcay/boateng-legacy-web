@extends('layouts.base')

@section('body')
	@include('partials.header')

	<section>
		<h1>
            Suggest a new name<br />
			<small>
				<a href="{{ route('language.create')  }}">&rarr; or click here to suggest a language</a>
			</small>
		</h1>
        <br />
        <br />

        <form class="form edit" method="post" name="definition" action="{{ route('definition.store') }}">
			{!! csrf_field() !!}
			<input type="hidden" name="type" value="{{ $type }}">
			<input type="hidden" name="relations[language][]" value="{{ $lang->code }}">

			{{-- Word --}}
			<div class="row center">
				TODO
            </div>

			<!-- Form actions -->
            <br />
            <br />
			<div class="row center">
				<input type="submit" name="next" value="continue" disabled>
				<input type="submit" name="next" value="finish" disabled>
			</div>
		</form>
	</section>

	@include('partials.footer')
@stop
