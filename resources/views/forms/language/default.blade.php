@extends('layouts.base')

@section('body')
	@include('layouts.header')
    
	<section>
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

        @if ($lang->exists)
            <form class="form edit" method="post" action="{{ route('language.update', ['code' => $lang->code]) }}">
                <input type="hidden" name="_method" value="PUT">
        @else
            <form class="form edit" method="post" action="{{ route('language.store') }}">
        @endif
		
			<!-- Title -->
			<div class="row">
				<input type="text" name="name" class="text-input" placeholder="e.g. Swahili" value="{{ $lang->getName() }}" required />
				<label for="name">Name</label>
			</div>
			
			<!-- Alternate spellings -->
			<div class="row">
				<input type="text" name="alt" class="text-input" placeholder="e.g. Kiswahili" value="{{ $lang->getAltNames() }}" />
				<label for="alt">Alternate names or spellings (seperated by ",")</label>
			</div>
			
			<!-- ISO-639-3 -->
			<div class="row">
				<input type="text" name="code" class="en-text-input" placeholder="e.g. swa" value="{{ $lang->code }}" {{ $lang->exists ? 'disabled' : 'required' }} />
				<label for="code"><a href="http://en.wikipedia.org/wiki/ISO_639-3" target="_blank">ISO-639-3</a> language code</label>
			</div>
			
			<!-- Parent language -->
			<div class="row">
				<input type="text" name="parent" class="text-input remote" value="{{ $lang->parent }}" />
				<label for="parent">Parent language (if applicable)</label>
			</div>
			
			<!-- Countries -->
			<div class="row">
                {!! Form::select(
                    'countries[]',
                    $lang->getCountryList(),
                    explode(',', $lang->countries),
                    [
                        'class' => 'en-text-input',
                        'multiple' => 'multiple'
                    ]
                ) !!}
				<label for="countries[]">Countries in which language is spoken</label>
			</div>
			
            <!-- Language notes -->
			<div class="row">
                <textarea name="desc" class="en-text-input" data-provide="markdown" placeholder="This language is so interesting because...">{{ $lang->desc }}</textarea>
				<label for="desc">Fun facts!</label>
			</div>
			
			<!-- Form actions -->
			<div class="row center">
				<input type="button" name="cancel" value="cancel" onclick="return confirm('Cancel editing?') ? App.redirect('') : false;" />
				<input type="submit" name="submit" value="save" />
			</div>
			
			{!! Form::token() !!}
			<!-- {{ $lang->getId() }} -->
		</form>
	</section>
    
    <script type="text/javascript">
    
    // Setup language search for "parent" field
    Forms.setupLangSearch(
        'input[name="parent"]',
        [{ code: '{{ $lang->parent }}', name: '{{ $lang->getParam('parentName') }}' }]
    );
    
    // Country selection
    $('select[name="countries[]"]').selectize({persist: false, maxItems: 20, plugins: ['remove_button']});
    
    $(document).ready(function() { $('input[name="name"]').focus(); });
    
    </script>

	@include('layouts.footer')
@stop
