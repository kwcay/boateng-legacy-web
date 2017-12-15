@extends('layouts.half-hero')

@section('hero')

    <h1 class="hero-title">
        Learn Something New
    </h1>

@stop

@section('body')

    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-lg-6 col-lg-offset-3">
            Pick a language you'd like to learn <strong>for free</strong>.
            <hr>
        </div>
    </div>

    <div class="row">
        @foreach ($languages as $lang)
            <div class="col-md-4">
                <div class="shaded-well">
                    <a href="{{ route('language.learn', $lang['code']) }}" class="card-btn shade-50">
                        <h3>{{ $lang['name'] }}</h3>

                        {{ $lang['regions'] }}
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-lg-6 col-lg-offset-3">
            <br>

            Or find other languages below
            <hr>
        </div>
    </div>

    @include('partials.search.form')

@stop
