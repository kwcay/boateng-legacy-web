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

                {{-- Definition --}}
                @include('pages.partials.'. $def->type .'-definition')
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
