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
        <small>
            @lang('branding.language_tag_line', ['lang' => $lang->name])
        </small>
    </h1>

    @if ($lang->definitions->count())

        {{-- Form --}}
        @include('partials.dictionary-form', ['code' => $lang->code])

        {{-- Results / Twitter pitch --}}
        <div id="results">
            <div class="text-center">
                Use this <em>&#10548;</em> to lookup words<br>
                and sayings in {{ $lang->name }}.
            </div>
        </div>
        <br>

        {{-- Language details --}}
        <h3>A little background on {{ $lang->name }}...</h3>
        <br>

        <div class="row">
            <div class="col-lg-6 meta">
                <ul class="fa-ul">
                    @if (strlen($lang->altNames))
                    <li>
                        <i class="fa-li fa fa-asterisk"></i>
                        Also refered to as: {{ $lang->altNames }}.
                    </li>
                    @endif

                    @if (count($lang->countries))
                    <li>
                        <i class="fa-li fa fa-asterisk"></i>
                        Spoken in {{ $lang->countries->implode('name', ', ') }}.
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
                                {{ $first->titles[0]->title }}
                            </a>
                            @endif

                            @if ($latest)
                            , and the latest word is
                            <a href="{{ $latest->uri }}">
                                {{ $latest->titles[0]->title }}
                            </a>
                            @endif
                        .
                    </li>

                    @if ($random)
                    <li>
                        <i class="fa-li fa fa-asterisk"></i>
                        <a href="{{ $random->uri }}">
                            {{ $random->titles[0]->title }}
                        </a>

                        is a random word in {{ $lang->name }}.
                    </li>
                    @endif
                </ul>
            </div>

            <div class="col-lg-6 meta">
                <div class="emphasis">
                    Know a thing or two in <em>{{ $lang->name }}</em>?
                    <br>

                    Suggest your own
                    <a href="{{ route('definition.create.word', ['lang' => $lang->code]) }}">words</a>
                    or <a href="{{ route('definition.create.expression', ['lang' => $lang->code]) }}">sayings</a>.
                </div>
            </div>
        </div>

    @else
    <div class="center">
        We have no words in this language yet.<br>
        <em><a href="{{ route('definition.create.word', ['lang' => $lang->code]) }}">Be the first to add one!</a></em>
    </div>
    @endif

@stop
