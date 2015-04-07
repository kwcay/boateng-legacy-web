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
				<a href="edit/lang">&rarr; or click here to suggest a language</a>
			</small>
		</h1>
		<form class="form edit" method="post" action="{{ URL::to('api/def') }}">
		
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
                {!! Form::select('type', $wordTypes, $def->getParam('type'), array('class' => 'en-text-input')) !!}
				<label for="type">Word type</label>
			</div>
			
            <!-- Translation -->
            <div class="row">
                <input type="text" name="translation[eng]" class="en-text-input" placeholder="e.g. love" value="{{ $def->getTranslation('eng') }}" />
                <label for="translation[eng]">English translation</label>
            </div>
            
            <!-- Meaning -->
            <div class="row">
                <input type="text" name="meaning[eng]" class="en-text-input" placeholder="e.g. an intense feeling of deep affection." value="{{ $def->getMeaning('eng') }}" />
                <label for="meaning[eng]">English meaning</label>
            </div>
			
			<!-- Language -->
			<div class="row">
				<input id="language" type="text" name="language" class="text-input remote" value="{{ $def->language }}" />
				<label for="language">Languages that use this word. Start typing and select a language from the list. You can drag these around (the first will be considred the "main" language).</label>
			</div>
			
			<!-- Form actions -->
			<div class="row center">
				<input type="button" name="cancel" value="cancel" onclick="return confirm('Cancel editing?') ? App.redirect('') : false;" />
				<input type="submit" name="submit" value="save" />
				<input type="button" name="new" value="create another" onclick="return App.redirect('edit')" />
			</div>
			
			{!! Form::token() !!}
			@if ($def->exists)
            <input type="hidden" name="id" value="{{ $def->getId() }}" />
            @endif
		</form>
	</section>
    
    <script type="text/javascript">
    
    // Word types
    //$('select[name="type"]').selectize();
    
    // Setup language search for "lanuguage" field
    Forms.setupLangSearch('input[name="language"]', {{ json_encode($options) }}, 20, ['remove_button', 'drag_drop']);
    
    $(document).ready(function() { $('input[name="word"]').focus(); });
    
    </script>

	@include('layouts.footer')
@stop
