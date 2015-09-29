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

        @if ($random)

        {{-- Search form --}}
        <br />
        @include('partials.lang-search', ['code' => $lang->code, 'name' => $lang->name])
        <br />
        <br />

        {{-- Language details --}}
        <h3>A little background...</h3>

        <div class="meta">

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

        </div>

        @if ($first)
        <div class="center">
            The first definition that was added to Di Nkɔmɔ in the <em>{{ $lang->name }}</em> language was
			<a href="{{ $first->uri }}">{{ $first->title }}</a>.
            The latest one to be added is
			<a href="{{ $latest->uri }}">{{ $latest->title }}</a>.
            <br /><br />
        </div>
        @endif

        {{-- Random word --}}
        <div class="emphasis">
            <i>A random word in {{ $lang->name }}:</i><br />
            <em>&ldquo; <a href="{{ $random->uri }}">{{ $random->title }}</a> &rdquo;</em>
        </div>
        <br />

        @else
        <div class="center">
            We have no definitions in this language yet.<br />
            <em><a href="{{ route('definition.create', ['lang' => $lang->code]) }}">Be the first to add one!</a></em>
        </div>
        @endif

	</section>

	@include('partials.footer')
@stop
