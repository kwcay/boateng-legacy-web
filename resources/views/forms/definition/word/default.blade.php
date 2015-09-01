@extends('layouts.base')

@section('body')
	@include('partials.header')

	<section>
		<h1>
            @if ($def->exists)
                Edit an <i>existing</i>
            @else
                Suggest a <i>new</i>
            @endif
            <br /><em>definition</em><br />
            @if ($def->exists)
                <small>
                    <a href="#" onclick="return App.openDialog('del');">
                        <span class="fa fa-trash-o"></span> click here to delete it
                    </a>
                </small><br />
            @endif
			<small>
				<a href="{{ route('language.create')  }}">&rarr; or click here to suggest a language</a>
			</small>
		</h1>

        @if ($def->exists)
            <form class="form edit" method="post" name="definition" action="{{ route('definition.update', ['id' => $def->getId()]) }}">
                <input type="hidden" name="_method" value="PUT">
        @else
            <form class="form edit" method="post" name="definition" action="{{ route('definition.store') }}">
        @endif

			{{-- Title --}}
			<div class="row">
				<input type="text" name="title" class="text-input" placeholder="e.g. ɔdɔ" value="{{ $def->title }}" />
				<label for="title">Title</label>
			</div>

			{{-- Alternate spellings --}}
			<div class="row">
				<input
                    type="text"
                    id="alt_titles"
                    name="alt_titles"
                    class="text-input"
                    placeholder="e.g. do, dɔ, odo "
                    value="{{ $def->alt_titles }}" />
				<label for="alt_titles">Alternate titles or spellings (seperated by ",")</label>
			</div>

			{{-- Type --}}
			<div class="row">
                {!! Form::select('sub_type', $def->getSubTypes(), $def->rawSubType, ['class' => 'en-text-input']) !!}
				<label for="type">Word type</label>
			</div>

            {{-- Translation --}}
            <div class="row">
                <input
                    type="text"
                    id="relations[translation][en]"
                    name="relations[translation][en]"
                    class="en-text-input"
                    placeholder="e.g. love"
                    value="{{ $def->getTranslation('en') }}" />
                <label for="relations[translation][en]">English translation</label>
            </div>

            {{-- Meaning --}}
            <div class="row">
                <input
                    type="text"
                    id="relations[meaning][en]"
                    name="relations[meaning][en]"
                    class="en-text-input"
                    placeholder="e.g. an intense feeling of deep affection."
                    value="{{ $def->getMeaning('en') }}" />
                <label for="relations[meaning][en]">English meaning</label>
            </div>

			{{-- Language --}}
			<div class="row">
                <select id="languages" class="text-input remote" name="relations[language]">
                    @foreach($def->languages as $lang)
                        <option value="{{ $lang->code }}" selected>
                            $lang->name
                        </option>
                    @endforeach
                </select>
				<label for="languages">
                    Languages that use this word. Start typing and select a language from the list.
                    You can drag these around (the first will be considred the "main" language).</label>
			</div>

			<!-- Form actions -->
			<div class="row center">
				<input type="submit" name="finish" value="done !" />
				<input type="submit" name="add" value="save + add" onclick="return saveAndNew();" />
				<input type="button" name="cancel" value="cancel" onclick="return confirm('Cancel editing?') ? window.history.back() : false;" />
			</div>

            {!! csrf_field() !!}
			<input type="hidden" name="more" value="0" />
		</form>
	</section>

    @if ($def->exists)
        <!-- Delete confirmation -->
        <div class="dialog del">
            <div>
                <a href="#" class="close">&#10005;</a>
                <h1>Really?</h1>
                <div class="center">
                    Are you sure you want to delete the definition for
                    <h2>&ldquo; {{ $def->title }} &rdquo;</h2>
                    for ever and ever?<br /><br />
                    <form class="form" method="post" name="delete" action="{{ route('definition.destroy', ['id' => $def->getId()]) }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="submit" name="confirm" value="yes, delete {{ $def->title }}" />
                        <input type="button" name="cancel" value="no, return" onclick="return App.closeDialogs()" />
			            {!! Form::token() !!}
                    </form>
                </div>
            </div>
        </div>
    @endif

    <script type="text/javascript">

    // Word types
    //$('select[name="type"]').selectize();

    // Setup language search for "lanuguage" field
    Forms.setupLangSearch('#languages', {!! json_encode($options) !!}, 20, ['remove_button', 'drag_drop']);

    var saveAndNew = function() {
        document.definition.more.value = 1;
        document.definition.submit();
    };

    //$(document).ready(function() { $('input[name="word"]').focus(); });

    </script>

	@include('partials.footer')
@stop
