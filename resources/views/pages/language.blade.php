@extends('layouts.narrow')

{{-- Page meta --}}
@section('title', $lang->name .' Language')
@section('description', 'Learn new words and sayings in '. $lang->name)
@section('keywords', implode(',', ['dictionary', $lang->name, $lang->code, 'free']))

@section('body')

    <h1>
        {{-- Edit button --}}
        @if (Auth::check())
            <span class="edit-res">
                <a href="{{ $lang->editUri }}" class="fa fa-pencil"></a>
            </span>
        @endif

        {{ $lang->name }}<br>
        <small>A Cultural Collection.</small>
    </h1>

    @if ($lang->definitions->count())

        {{-- Search form --}}
        @include('partials.lang-search', ['code' => $lang->code, 'name' => $lang->name])
        <br>

        <div class="emphasis">
            Know a thing or two about <em>{{ $lang->name }}</em>?
            <br>

            Suggest your own
            <a href="{{ route('definition.create.word', ['lang' => $lang->code]) }}">words</a>
            or <a href="{{ route('definition.create.phrase', ['lang' => $lang->code]) }}">sayings</a>.
        </div>
        <br>

        {{-- Language details --}}
        <h3>A little background on {{ $lang->name }}...</h3>
        <br>

        <div class="row">
            <!-- <div class="col-sm-12 col-md-6 meta">
                Lorem ipsum...
            </div> -->

            <div class="col-sm-12 col-md-6 meta">
                <ul class="fa-ul">
                    @if (strlen($lang->altNames))
                    <li>
                        <i class="fa-li fa fa-asterisk"></i>
                        Also refered to as: {{ $lang->altNames }}
                    </li>
                    @endif

                    @if ($lang->parentLanguage)
                    <li>
                        <i class="fa-li fa fa-asterisk"></i>
                        Is a child language of
                        <a href="{{ $lang->parentLanguage->uri }}">
                            {{ $lang->parentLanguage->name }}
                        </a>.
                    </li>
                    @endif

                    {{-- Definition count --}}
                    <li>
                        <i class="fa-li fa fa-asterisk"></i>
                        We have {{ $lang->definitions->count() }} {{ $lang->name }} words in our
                        database.
                    </li>

                    {{-- First and latest words. --}}
                    <li>
                        <i class="fa-li fa fa-asterisk"></i>

                            @if ($first)
                            The first word was
                            <a href="{{ $first->uri }}">
                                {{ $first->title }}
                            </a>
                            @endif

                            @if ($latest)
                            , and the latest word is
                            <a href="{{ $latest->uri }}">
                                {{ $latest->title }}
                            </a>
                            @endif
                        .
                    </li>

                    @if ($random)
                    <li>
                        <i class="fa-li fa fa-asterisk"></i>
                        <a href="{{ $random->uri }}">
                            {{ $random->title }}
                        </a>

                        is a random word in {{ $lang->name }}.
                    </li>
                    @endif
                </ul>
            </div>
        </div>

    @else
    <div class="center">
        We have no words in this language yet.<br>
        <em><a href="{{ route('definition.create.word', ['lang' => $lang->code]) }}">Be the first to add one!</a></em>
    </div>
    @endif

@stop
