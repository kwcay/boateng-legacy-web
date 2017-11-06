@extends('layouts.half-hero')

@section('hero')

    <h1 class="hero-title">
        <small>Learn</small>
        {{ $lang->name }}
    </h1>

@stop

@section('body')

    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-lg-6 col-lg-offset-3">
            We're building a language learning tool for
            <a href="{{ route('language.show', $lang->code) }}">{{ $lang->getFirstName() }}</a>.
            Sign up to receive updates on our progress, or if you'd like to help!
            <br>
            <br>

            @include('language.learn.signup', ['name' => $lang->name])
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-lg-6 col-lg-offset-3">
            @lang('branding.pitch')
            <hr>
        </div>
    </div>

@stop
