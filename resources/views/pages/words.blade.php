@extends('layouts.narrow')

@section('title', $query .' meaning in '. $lang->name)

@section('body')

    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 text-center">
            <h1 class="emphasis">
                {{ $query }}
            </h1>
        </div>
    </div>

    <div class="center pad-bottom">
        Is a word in <em><a href="{{ $lang->uri }}">{{ $lang->name }}</a></em> that translates to:
    </div>

    {{-- List of matching definitions --}}
    <div class="definitions">
        @foreach ($definitions as $def)
            <div class="definition">
                @if ($def->id != $definitions[0]->id)
                    <span class="or">or</span>
                @endif

                <h3>
                    @if (Auth::check())
                    <span class="edit-res">
                        <a href="{{ $def->editUri }}" class="fa fa-pencil"></a>
                    </span>
                    @endif

                    &ldquo; {{ $def->getPracticalTranslation('eng') }} &rdquo;

                    {{-- Sub type --}}
                    <small>
                        <code>{{ $def->subType }}</code>
                    </small>
                </h3>

                {{-- Append meaning --}}
                @if ($def->hasMeaning('eng'))
                    {{ $def->getMeaning('eng') }}.
                @endif

                {{-- Append literal meaning --}}
                @if ($def->hasLiteralTranslation('eng'))
                    <i>Literally</i>: {{ $def->getLiteralTranslation('eng') }}.
                @endif

                {{-- Append alternative spellings --}}
                @if (strlen($def->altTitles))
                    <i>Alternatively</i>: {{ $def->altTitles }}.
                @endif

                {{-- Add language information --}}
                @if (count($def->languages) > 1)
                    <i>Also a word in: </i>
                    @foreach ($def->languages as $otherLang)
                        @if ($otherLang->code != $lang->code)
                            <a href="{{ $otherLang->uri }}">{{ $otherLang->name }}</a>
                        @endif
                    @endforeach
                @endif

                {{-- Back search --}}
                {{-- Only add a break if there was more info appended to this definition --}}
                @if ($def->hasMeaning('eng') ||
                    $def->hasLiteralTranslation('eng') ||
                    strlen($def->altTitles ||
                    count($def->languages) > 1))
                    <br>
                @endif
                <a class="more" href="{{ route('home', ['q' => $def->getPracticalTranslation('eng')]) }}">
                    &rarr; more translations for {{ $def->getPracticalTranslation('eng') }}
                </a>
            </div>
        @endforeach
    </div>
    <br>

    <div class="">
        Suggest a new
        <a href="{{ route('definition.create.word', ['lang' => $lang->code]) }}">word</a>
        or <a href="{{ route('definition.create.phrase', ['lang' => $lang->code]) }}">saying</a>,
        or lookup other things in {{ $lang->name }}:
    </div>
    <br>

    {{-- Search form --}}
    @include('partials.lang-search', ['code' => $lang->code, 'name' => $lang->name, 'msg' => false])

@stop
