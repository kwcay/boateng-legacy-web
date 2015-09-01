@extends('layouts.base')

@section('body')
	@include('partials.header')

	<section>

	    {{-- Page title --}}
		<h1>
            @if ($lang->exists)
                Edit an <i>existing</i>
            @else
                Suggest a <i>new</i>
            @endif
            <br /><em>language</em><br />
			<small>
				<a href="{{ route('definition.create')  }}">&rarr; or click here to suggest a new definition</a>
			</small>
		</h1>

        {{-- Form begins --}}
        @if ($lang->exists)
            <form class="form edit" method="post" action="{{ route('language.update', ['code' => $lang->code]) }}">
                <input type="hidden" name="_method" value="PUT">
        @else
            <form class="form edit" method="post" action="{{ route('language.store') }}">
        @endif

			{{-- Title row --}}
			<div class="row">
				<input type="text" name="name" class="text-input" placeholder="e.g. Swahili" value="{{ $lang->name }}" required />
				<label for="name">Name</label>
			</div>

			{{-- Alternate names/spellings --}}
			<div class="row">
				<input type="text" name="alt_names" class="text-input" placeholder="e.g. Kiswahili" value="{{ $lang->alt_names }}" />
				<label for="alt">Alternate names or spellings (seperated by ",")</label>
			</div>

			{{-- ISO-639-3 --}}
			<div class="row">
				<input
                    type="text"
                    name="code"
                    class="en-text-input"
                    placeholder="e.g. swa"
                    value="{{ $lang->code }}"
                    {{ $lang->exists ? 'disabled' : 'required' }} />

				<label for="code">
                    <a href="http://en.wikipedia.org/wiki/ISO_639-3" target="_blank">ISO-639-3</a>
                    language code
                </label>
			</div>

			{{-- Parent language --}}
			<div class="row">
				<input type="text" name="parent_code" class="text-input remote" value="{{ $lang->parent_code }}" />
				<label for="parent">Parent language (if applicable)</label>
			</div>

			{{-- Country list --}}
			<div class="row">
                <select id="countries" name="countries[]" class="en-text-input" multiple>
                    @foreach($lang->getCountryList() as $code => $name)
                        <option
                            value="{{ $code }}"
                            {{ in_array($code, explode(',', $lang->countries)) ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
				<label for="countries">Countries in which language is spoken</label>
			</div>

            {{-- Language notes --}}
			<div class="row">
                <textarea
                    name="desc[en]"
                    class="en-text-input"
                    placeholder="This language is so interesting because..."
                    disabled></textarea>
				<label for="desc">Fun facts!</label>
			</div>

			{{-- Form actions --}}
			<div class="row center">
                <input type="submit" value="save" />
				<input type="button" value="cancel" onclick="return confirm('Cancel editing?') ? window.history.back() : false;" />
			</div>

            {!! csrf_field() !!}
		</form>
	</section>

    <script type="text/javascript">

    // Setup language search for "parent" field
    Forms.setupLangSearch('#parent_code', [{code: '{{ $lang->parent_code }}', name: '{{ $lang->getParam('parentName') }}'}], 1);

    // Country selection
    $('#countries').selectize({persist: false, maxItems: 20, plugins: ['remove_button']});

    $(document).ready(function() { $('input[name="name"]').focus(); });

    </script>

	@include('partials.footer')
@stop
