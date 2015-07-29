@extends('layouts.base')

@section('title', $lang->name .' - the book of native tongues.')

@section('body')
	@include('layouts.header')
	
	<section>
		<h1>
            <span class="edit-res">
                <a href="{{ $lang->getEditUri() }}" class="fa fa-pencil"></a>
            </span>
            {{ $lang->name }}
        </h1>
        
        @if ($random)
        <br />
        <div class="emphasis">
            <i>A random word in {{ $lang->name }}:</i><br />
            <em>&ldquo; <a href="{{ $random->getUri() }}">{{ $random->data }}</a> &rdquo;</em><br />
            <small><a href="{{ route('language.show', ['code' => $lang->code]) }}">more</a></small>
        </div>
        <br />
        
        @else
        <div class="center">
            We have no words in this language yet.<br />
            <em><a href="{{ route('definition.create', ['lang' => $lang->code]) }}">Be the first to add one!</a></em>
        </div>
        @endif

        @if ($random)
        <h2>A little background...</h2>
        <div class="center">
            The first word that was added to Di Nkɔmɔ for <em>{{ $lang->name }}</em> is : [to do].
            The latest word to be added was: [to do].
            <br /><br />

            @if (strlen($lang->desc))
                {{ $lang->getDescription() }}
            @endif
        </div>
        @endif
		
	</section>

	@include('layouts.footer')
@stop
