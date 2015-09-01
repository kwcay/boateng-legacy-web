@extends('layouts.base')

@section('title', $lang->name .' - the book of native tongues.')

@section('body')
	@include('partials.header')

	<section>
		<h1>
            {{-- Edit button --}}
            @if (Auth::check())
                <span class="edit-res">
                    <a href="{{ $lang->editUri }}" class="fa fa-pencil"></a>
                </span>
            @endif

            {{ $lang->name }}
        </h1>

        {{-- Language details --}}
        <h3>A little background...</h3>
        <div class="ui two column divided grid">
            <div class="stretched row">

                {{-- Background --}}
                <div class="column">
                    A little background.
                </div>

                {{-- Meta --}}
                <div class="column">
                    Meta
                </div>
            </div>
        </div>


                            {{-- Alternate spellings --}}
                            @if (strlen($lang->alt_names))
                                <div class="row">
                                    Alternatively: <em>{{ $lang->alt_names }}</em>
                                </div>
                            @endif

                            {{-- Total words --}}
                            <div class="row">
                                Total words: <em>{{ $lang->definitions()->count() }}</em>
                            </div>

                            {{-- Parent language --}}
                            @if ($lang->parent)
                                <div class="row">
                                    Parent language: <em><a href="{{ $lang->parent->uri }}">{{ $lang->parent->name }}</a></em>
                                </div>
                            @endif

                            {{-- Children languages --}}
                            @if (count($lang->children))

                                <div class="row">
                                    Child languages:
                                    @foreach ($lang->children as $child)
                                        <em><a href="{{ $child->uri }}">{{ $child->name }}</a></em>
                                        {{ $lang->children->last()->code == $child->code ? '.' : ', ' }}
                                    @endforeach
                                </div>
                            @endif

        @if ($first)
        <div class="center">
            The first definition that was added to Di Nkɔmɔ in the <em>{{ $lang->name }}</em> language was
			<a href="{{ $first->getUri() }}">{{ $first->title }}</a>.
            The latest one to be added is
			<a href="{{ $latest->getUri() }}">{{ $latest->title }}</a>.
            <br /><br />
        </div>
        @endif

        {{-- Meta data --}}
        <div class="meta ui container">

        </div>

        @include('partials.lang-search')

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

	</section>

	@include('partials.footer')
@stop
