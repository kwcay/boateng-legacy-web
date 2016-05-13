@extends('layouts.narrow')

@section('title', $query .' meaning in '. $lang->name)

@section('body')

    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 text-center">
            <h1>
                &ldquo; {{ $query }} &rdquo;
            </h1>
        </div>
    </div>

    <div class="center pad-bottom">
        Is an expression in <em><a href="{{ $lang->uri }}">{{ $lang->name }}</a></em> that translates to:
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

    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 text-center">
            <div class="emphasis">
                Know a thing or two in <em>{{ $lang->name }}</em>?
                <br>

                Suggest your own
                <a href="{{ route('definition.create.word', ['lang' => $lang->code]) }}">words</a>
                or <a href="{{ route('definition.create.expression', ['lang' => $lang->code]) }}">sayings</a>.
            </div>
        </div>
    </div>

@stop
