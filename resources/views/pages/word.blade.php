@extends('layouts.base')

@section('title', '[QUERY] is a word in [LANG] - Di Nkomo: the book of native tongues.')

@section('body')
	@include('layouts.header')
	
	<section>
		<h1>{{ $query }}</h1>
        
        <div class="center pad-bottom">
            Is a word in <em><a href="{{ $lang->getUri() }}">{{ $lang->getName() }}</a></em> that could translate to:
        </div>
        
        <div class="definitions">
            @for ($i = 0; $i < count($words); $i++)
                <div class="definition">
                    @if ($i > 0)
                        <span class="or">or</span>
                    @endif
                    <h3>
                        <span class="edit-res">
                            <a href="{{ $words[$i]->getEditUri() }}" class="fa fa-pencil"></a>
                        </span>
                        &ldquo; {{ $words[$i]->getTranslation('en') }} &rdquo;
                    </h3>
                    @if (strlen($words[$i]->getMeaning('en')))
                        {{ $words[$i]->getMeaning('en') }}.
                    @endif
                    @if (strlen($words[$i]->getAltWords()))
                        Alternatively: {{ $words[$i]->getAltWords() }}
                    @endif
                </div>
            @endfor
        </div>
        
	</section>

	@include('layouts.footer')
@stop
