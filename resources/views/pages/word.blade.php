@extends('layouts.base')

@section('head.title')
	<title>{{ $query }} is a word in {{ $lang->getName() }} - Di Nkomo: the book of native tongues.</title>
@stop

@section('body')
	@include('layouts.header')
	
	<section>
		<h1>{{ $query }}</h1>
        
        <div class="center pad-bottom">
            Is a word in <em><a href="{{ $lang->getUri() }}">{{ $lang->getName() }}</a></em> that could translate to:
        </div>
        
        @for ($i = 0; $i < count($words); $i++)
        <div style="position: relative; margin-left: 30px;">
            @if ($i > 0)
                <i style="position: absolute; left: -40px; top: 4px">or</i>
            @endif
            <h3>
                <span class="edit-res">
                    <a href="{{ $words[$i]->getEditUri() }}" class="fa fa-pencil"></a>
                </span>
                &ldquo; {{ $words[$i]->getTranslation('en') }} &rdquo;
            </h3>
            @if (strlen($words[$i]->getMeaning('en')))
                &mdash; {{ $words[$i]->getMeaning('en') }}
            @endif
            @if (strlen($words[$i]->getAltWords()))
                Alternate spellings: {{ $words[$i]->getAltWords() }}
            @endif
        </div>
        @endfor
        
	</section>

	@include('layouts.footer')
@stop
