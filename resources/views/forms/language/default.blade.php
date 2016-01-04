@extends('layouts.narrow')

@section('body')

	<section>

	    {{-- Page title --}}
		<h1>
            Edit an <i>existing</i>
            <br>

            <em>language</em>
		</h1>

        {{-- Form begins --}}
        <form
            class="form edit"
            method="post"
            action="{{ route('language.update', ['code' => $lang->code]) }}">

            <input type="hidden" name="_method" value="PUT">
            {!! csrf_field() !!}

			{{-- Title row --}}
			<div class="row">
				<input
                    type="text"
                    name="name"
                    class="text-input"
                    placeholder="e.g. Swahili"
                    value="{{ $lang->name }}"
                    autocomplete="off"
                    required>
				<label for="name">Name</label>
			</div>

			{{-- Alternate names/spellings --}}
			<div class="row">
				<input
                    type="text"
                    name="altNames"
                    class="text-input"
                    placeholder="e.g. Kiswahili"
                    value="{{ $lang->altNames }}"
                    autocomplete="off">
				<label for="altNames">Alternate names or spellings (seperated by ",")</label>
			</div>

			{{-- ISO-639-3 --}}
			<div class="row">
				<input
                    type="text"
                    name="code"
                    class="en-text-input"
                    placeholder="e.g. swa"
                    value="{{ $lang->code }}"
                    disabled>

				<label for="code">
                    <a href="http://en.wikipedia.org/wiki/ISO_639-3" target="_blank">ISO-639-3</a>
                    language code
                </label>
			</div>

			{{-- Parent language --}}
			<div class="row">
				<input
                    type="text"
                    id="parentCode"
                    name="parentCode"
                    class="text-input remote"
                    value="{{ $lang->parentCode }}">
				<label for="parent">Parent language (if applicable)</label>
			</div>

			{{-- Country list --}}
			<div class="row">
                <select id="countries" name="countries[]" class="en-text-input" multiple disabled>
                    {{-- @foreach($lang->getCountryList() as $code => $name)
                        <option
                            value="{{ $code }}"
                            {{ in_array($code, explode(',', $lang->countries)) ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach --}}
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
                <input type="submit" value="save">
				<input
                    type="button"
                    value="cancel"
                    onclick="return confirm('Cancel editing?') ? window.history.back() : false;">
			</div>
		</form>
	</section>

    <script type="text/javascript">

    // Setup language search for "parent" field
    @if ($lang->parent)
        Forms.setupLangSearch('#parentCode', [{
            code: '{{ $lang->parent->code }}',
            name: '{{ $lang->parent->name }}'
        }], 1);
    @else
        Forms.setupLangSearch('#parentCode', [], 1);
    @endif

    // Country selection
    $('#countries').selectize({persist: false, maxItems: 20, plugins: ['remove_button']});

    $(document).ready(function() { $('input[name="name"]').focus(); });

    </script>

@stop
