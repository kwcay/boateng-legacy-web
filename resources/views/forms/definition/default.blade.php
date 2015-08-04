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
		
			<!-- Word -->
			<div class="row">
				<input type="text" name="word" class="text-input" placeholder="e.g. ɔdɔ" value="{{ $def->data }}" />
				<label for="word">Word</label>
			</div>
			
			<!-- Alternate spellings -->
			<div class="row">
				<input type="text" name="alt" class="text-input" placeholder="e.g. do, dɔ, odo " value="{{ $def->alt_data }}" />
				<label for="alt">Alternate words or spellings (seperated by ",")</label>
			</div>
			
			<!-- Type -->
			<div class="row">
                {!! Form::select('type', $def->partsOfSpeech, $def->type, array('class' => 'en-text-input')) !!}
				<label for="type">Word type</label>
			</div>
			
            <!-- Translation -->
            <div class="row">
                <input type="text" name="translations[en]" class="en-text-input" placeholder="e.g. love" value="{{ $def->getTranslation('en') }}" />
                <label for="translations[en]">English translation</label>
            </div>
            
            <!-- Meaning -->
            <div class="row">
                <input type="text" name="meanings[en]" class="en-text-input" placeholder="e.g. an intense feeling of deep affection." value="{{ $def->getMeaning('en') }}" />
                <label for="meanings[en]">English meaning</label>
            </div>
			
			<!-- Language -->
			<div class="row">
				<input id="languages" type="text" name="languages" class="text-input remote" value="{{ implode(', ', $def->languages) }}" />
				<label for="languages">Languages that use this word. Start typing and select a language from the list. You can drag these around (the first will be considred the "main" language).</label>
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

    @if ($def->exists)
        <!-- Delete confirmation -->
        <div class="dialog del">
            <div>
                <a href="#" class="close">&#10005;</a>
                <h1>Really?</h1>
                <div class="center">
                    Are you sure you want to delete the definition for
                    <h2>&ldquo; {{ $def->data }} &rdquo;</h2>
                    for ever and ever?<br /><br />
                    <form class="form" method="post" name="delete" action="{{ route('definition.destroy', ['id' => $def->getId()]) }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="submit" name="confirm" value="yes, delete {{ $def->getWord() }}" />
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
    Forms.setupLangSearch('input[name="language"]', {!! json_encode($options) !!}, 20, ['remove_button', 'drag_drop']);

    var saveAndNew = function() {
        document.definition.more.value = 1;
        document.definition.submit();
    };
    
    //$(document).ready(function() { $('input[name="word"]').focus(); });
    
    </script>

	@include('layouts.footer')
@stop
