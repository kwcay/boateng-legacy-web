@extends('layouts.narrow')

@section('body')

    <h1>
        Di Nkɔmɔ<br>

        <small>
            A Cultural Reference.
        </small>
    </h1>

    {{-- Form --}}
    @include('partials.dictionary-form')

    {{-- Results / Twitter pitch --}}
    <div id="results">
        <div class="text-center">
            Di Nkɔmɔ is a free <a href="{{ route('about') }}">cultural reference</a> <br>
            for the gems of the world.
        </div>
    </div>

@stop
