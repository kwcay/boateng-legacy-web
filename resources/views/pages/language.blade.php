@extends('layouts.base')

@section('title', $lang->name .' - the book of native tongues.')

@section('body')
	@include('partials.header')

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
            <em>&ldquo; <a href="{{ $random->getUri() }}">{{ $random->title }}</a> &rdquo;</em><br />
            <small><a href="{{ route('language.show', ['code' => $lang->code]) }}">more</a></small>
        </div>
        <br />

        @else
        <div class="center">
            We have no words in this language yet.<br />
            <em><a href="{{ route('definition.create', ['lang' => $lang->code]) }}">Be the first to add one!</a></em>
        </div>
        @endif

        @if ($first)
        <h2>A little background...</h2>
        <div class="center">
            The first definition that was added to Di Nkɔmɔ in the <em>{{ $lang->name }}</em> language was
			<a href="{{ $first->getUri() }}">{{ $first->title }}</a>.
            The latest one to be added is
			<a href="{{ $latest->getUri() }}">{{ $latest->title }}</a>.
            <br /><br />
        </div>
        @endif

	</section>

	@include('partials.footer')
@stop
