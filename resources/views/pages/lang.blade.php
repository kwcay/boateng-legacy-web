@extends('layouts.base')

@section('head.title')
	<title>{{ $lang->getName() }} - the book of native tongues.</title>
@stop

@section('body')
	@include('layouts.header')
	
	<section>
		<h1>
            <span class="edit-res">
                <a href="{{ $lang->getEditUri() }}" class="fa fa-pencil"></a>
            </span>
            {{ $lang->getName() }}
        </h1>
        
        @if ($random)
        <br />
        <div class="emphasis">
            <i>A random word in {{ $lang->getName() }}:</i><br />
            <em>&ldquo; <a href="{{ $random->getWordUri() }}">{{ $random->getWord() }}</a> &rdquo;</em><br />
            <small><a href="{{ route('language.show', ['code' => $lang->code]) }}">more</a></small>
        </div>
        <br />
        
        @else
        <div class="center">
            We have no words in this language yet.<br />
            <em><a href="{{ route('definition.create', ['lang' => $lang->code]) }}">Be the first to add one!</a></em>
        </div>
        @endif
        
        @if (strlen($lang->desc))
        <h2>A little background</h2>
        <div class="center">
            {{ $lang->getDescription() }}
        </div>
        @endif
		
	</section>

	@include('layouts.footer')
@stop
