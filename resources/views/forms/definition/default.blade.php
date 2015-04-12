@extends('layouts.base')

@section('body')
	@include('layouts.header')

	<section>
		<h1>
            @if ($def->exists)
                Edit an <i>existing</i>
            @else
                Suggest a <i>new</i>
            @endif
            <br /><em>definition</em><br />
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
		
			<!-- Word -->
			<div class="row">
				<input type="text" name="word" class="text-input" placeholder="e.g. ɔdɔ" value="{{ $def->getWord() }}" />
				<label for="word">Word</label>
			</div>
			
			<!-- Alternate spellings -->
			<div class="row">
				<input type="text" name="alt" class="text-input" placeholder="e.g. do, dɔ, odo " value="{{ $def->getAltWords() }}" />
				<label for="alt">Alternate words or spellings (seperated by ",")</label>
			</div>
			
			<!-- Type -->
			<div class="row">
                {!! Form::select('type', $def->wordTypes, $def->getParam('type'), array('class' => 'en-text-input')) !!}
				<label for="type">Word type</label>
			</div>
			
            <!-- Translation -->
            <div class="row">
                <input type="text" name="translation[en]" class="en-text-input" placeholder="e.g. love" value="{{ $def->getTranslation('en') }}" />
                <label for="translation[en]">English translation</label>
            </div>
            
            <!-- Meaning -->
            <div class="row">
                <input type="text" name="meaning[en]" class="en-text-input" placeholder="e.g. an intense feeling of deep affection." value="{{ $def->getMeaning('en') }}" />
                <label for="meaning[en]">English meaning</label>
            </div>
			
			<!-- Language -->
			<div class="row">
				<input id="language" type="text" name="language" class="text-input remote" value="{{ $def->language }}" />
				<label for="language">Languages that use this word. Start typing and select a language from the list. You can drag these around (the first will be considred the "main" language).</label>
			</div>
			
			<!-- Form actions -->
			<div class="row center">
				<input type="submit" name="finish" value="done !" />
				<input type="submit" name="new" value="save + add" onclick="return saveAndNew();" />
				<input type="button" name="cancel" value="return" onclick="return confirm('Cancel editing?') ? App.redirect('') : false;" />
			</div>
			
			{!! Form::token() !!}
			<input type="hidden" name="more" value="0" />
		</form>
	</section>
    
    <script type="text/javascript">
    
    // Word types
    //$('select[name="type"]').selectize();
    
    // Setup language search for "lanuguage" field
    Forms.setupLangSearch('input[name="language"]', {!! json_encode($options) !!}, 20, ['remove_button', 'drag_drop']);

    var saveAndNew = function() {
        document.definition.more.value = 1;
        document.definition.submit();
    };
    
    //$(document).ready(function() { $('input[name="word"]').focus(); });
    
    </script>

	@include('layouts.footer')
@stop
